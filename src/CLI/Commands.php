<?php

namespace SimpleCMP\WpSimpleCmp\CLI;

use SimpleCMP\WpSimpleCmp\Service\BridgeSecretProvider;
use SimpleCMP\WpSimpleCmp\Service\CanonicalJsonEncoder;
use SimpleCMP\WpSimpleCmp\Database\Repositories\ConfigSnapshotRepository;
use SimpleCMP\WpSimpleCmp\Database\Repositories\AuditRetentionLogRepository;
use SimpleCMP\WpSimpleCmp\Database\Repositories\ConsentLogRepository;
use SimpleCMP\WpSimpleCmp\Service\VisitorAuskunftService;

class Commands
{
    public static function register(): void
    {
        \WP_CLI::add_command('simplecmp generate-bridge-secret', [self::class, 'generateBridgeSecret']);
        \WP_CLI::add_command('simplecmp snapshot-config', [self::class, 'snapshotConfig']);
        \WP_CLI::add_command('simplecmp audit-retention', [self::class, 'auditRetention']);
        \WP_CLI::add_command('simplecmp export-audit', [self::class, 'exportAudit']);
    }

    /**
     * Generate a fresh bridge secret for HMAC webhook authentication.
     *
     * ## OPTIONS
     *
     * [--force]
     * : Overwrite existing secret
     *
     * ## EXAMPLES
     *
     *     wp simplecmp generate-bridge-secret
     *     wp simplecmp generate-bridge-secret --force
     */
    public static function generateBridgeSecret(array $args, array $assocArgs): void
    {
        $provider = new BridgeSecretProvider();

        if ($provider->hasSecret() && empty($assocArgs['force'])) {
            \WP_CLI::warning('Bridge secret already exists. Use --force to overwrite.');
            return;
        }

        $secret = $provider->generate();

        \WP_CLI::success('Bridge secret generated: ' . $secret);
        \WP_CLI::log('Add to wp-config.php: define(\'SIMPLECMP_BRIDGE_SECRET\', \'' . $secret . '\');');
    }

    /**
     * Capture a config snapshot for audit purposes.
     *
     * ## OPTIONS
     *
     * [--site=<site>]
     * : Site identifier (default: 'default')
     *
     * [--all-sites]
     * : Snapshot all sites
     *
     * ## EXAMPLES
     *
     *     wp simplecmp snapshot-config --site=default
     *     wp simplecmp snapshot-config --all-sites
     */
    public static function snapshotConfig(array $args, array $assocArgs): void
    {
        $site = $assocArgs['site'] ?? 'default';

        global $wpdb;
        $serviceRepo = new \SimpleCMP\WpSimpleCmp\Database\Repositories\ServiceRepository($wpdb);
        $themeRepo = new \SimpleCMP\WpSimpleCmp\Database\Repositories\ThemeRepository($wpdb);
        $overrideRepo = new \SimpleCMP\WpSimpleCmp\Database\Repositories\TranslationOverrideRepository($wpdb);
        $snapshotRepo = new ConfigSnapshotRepository($wpdb);
        $encoder = new CanonicalJsonEncoder();

        $config = [
            'services' => array_map(function ($s) {
                return [
                    'service_id' => $s->service_id,
                    'name' => $s->name,
                    'vendor' => $s->vendor,
                    'purposes' => json_decode($s->purposes ?? '[]', true),
                    'origins' => json_decode($s->origins ?? '[]', true),
                    'cookies' => json_decode($s->cookies ?? '[]', true),
                ];
            }, $serviceRepo->findAll()),
        ];

        $canonicalJson = $encoder->encode($config);
        $versionHash = hash('sha256', $canonicalJson);

        $result = $snapshotRepo->insert($site, $versionHash, $canonicalJson, 'cli', get_current_user_id());

        if ($result === null) {
            \WP_CLI::log('Snapshot already exists (identical content). Hash: ' . $versionHash);
        } else {
            \WP_CLI::success('Snapshot created. Hash: ' . $versionHash);
        }
    }

    /**
     * Run DSGVO retention on audit tables.
     *
     * ## OPTIONS
     *
     * --target=<target>
     * : Table to clean: consent-log, config-snapshot, all
     *
     * --keep-days=<days>
     * : Minimum retention in days
     *
     * --reason=<reason>
     * : Legal reason (min 30 chars)
     *
     * [--site=<site>]
     * : Site identifier (default: 'default')
     *
     * [--dry-run]
     * : Count only, no actual deletion
     *
     * --i-know-what-i-do
     * : Required safety flag
     *
     * [--allow-aggressive-retention]
     * : Allow keep-days under 90
     *
     * ## EXAMPLES
     *
     *     wp simplecmp audit-retention --target=all --keep-days=1095 --reason="DSGVO Art. 5 (1) (e) — 3 years retention" --i-know-what-i-do
     */
    public static function auditRetention(array $args, array $assocArgs): void
    {
        $target = $assocArgs['target'] ?? '';
        $keepDays = (int) ($assocArgs['keep-days'] ?? 0);
        $reason = $assocArgs['reason'] ?? '';
        $site = $assocArgs['site'] ?? 'default';
        $dryRun = isset($assocArgs['dry-run']);
        $confirmed = isset($assocArgs['i-know-what-i-do']);
        $aggressive = isset($assocArgs['allow-aggressive-retention']);

        if (!in_array($target, ['consent-log', 'config-snapshot', 'all'], true)) {
            \WP_CLI::error('--target must be one of: consent-log, config-snapshot, all');
        }

        if ($keepDays < 90 && !$aggressive) {
            \WP_CLI::error('keep-days under 90 requires --allow-aggressive-retention');
        }

        if (strlen($reason) < 30) {
            \WP_CLI::error('--reason must be at least 30 characters');
        }

        if (!$confirmed) {
            \WP_CLI::error('--i-know-what-i-do is required');
        }

        $cutoff = time() - ($keepDays * 86400);

        global $wpdb;
        $retentionLogRepo = new AuditRetentionLogRepository($wpdb);

        if (in_array($target, ['consent-log', 'all'], true)) {
            $consentLogRepo = new ConsentLogRepository($wpdb);
            $count = $dryRun
                ? $consentLogRepo->countBySite($site)
                : $consentLogRepo->deleteBySite($site);

            \WP_CLI::log(sprintf(
                '%s: %d rows from consent_log (site=%s)',
                $dryRun ? 'Would delete' : 'Deleted',
                $count,
                $site
            ));

            $retentionLogRepo->log([
                'target_table' => 'consent_log',
                'target_site' => $site,
                'rows_deleted' => $count,
                'keep_days' => $keepDays,
                'oldest_kept_crdate' => $cutoff,
                'invoked_by' => wp_get_current_user()->user_login,
                'invocation_reason' => $reason,
                'dry_run' => $dryRun ? 1 : 0,
            ]);
        }

        \WP_CLI::success('Retention complete.');
    }

    /**
     * Export audit bundle as JSON or CSV.
     *
     * ## OPTIONS
     *
     * --site=<site>
     * : Site identifier
     *
     * [--snapshot=<hash>]
     * : Export specific snapshot + decisions
     *
     * [--visitor=<uuid>]
     * : Export by visitor UUID (CLI-mediated Auskunft)
     *
     * [--since=<date>]
     * : Date range start (Y-m-d)
     *
     * --format=<format>
     * : json or csv
     *
     * [--output=<file>]
     * : Output file path
     *
     * ## EXAMPLES
     *
     *     wp simplecmp export-audit --site=default --snapshot=abcdef --format=json
     *     wp simplecmp export-audit --site=default --visitor=00000000-... --format=csv --output=/tmp/auskunft.csv
     */
    public static function exportAudit(array $args, array $assocArgs): void
    {
        $site = $assocArgs['site'] ?? '';
        $format = $assocArgs['format'] ?? 'json';
        $outputPath = $assocArgs['output'] ?? null;

        if (empty($site)) {
            \WP_CLI::error('--site is required');
        }

        global $wpdb;
        $snapshotRepo = new ConfigSnapshotRepository($wpdb);

        $export = [
            'schemaVersion' => 1,
            'exportedAt' => wp_date('c'),
            'exportedBy' => 'cli:' . wp_get_current_user()->user_login,
            'filter' => [
                'site' => $site,
            ],
        ];

        if (!empty($assocArgs['snapshot'])) {
            $hash = $assocArgs['snapshot'];
            $snapshot = $snapshotRepo->find($site, $hash);
            if ($snapshot) {
                $export['snapshots'] = [[
                    'id' => (int) $snapshot->id,
                    'version_hash' => $snapshot->version_hash,
                    'canonical' => json_decode($snapshot->canonical_json, true),
                    'created_at' => $snapshot->created_at,
                ]];
            }
        }

        $output = json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($outputPath) {
            file_put_contents($outputPath, $output);
            \WP_CLI::success('Exported to ' . $outputPath);
        } else {
            \WP_CLI::log($output);
        }
    }
}
