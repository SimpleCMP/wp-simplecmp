<?php

namespace SimpleCMP\WpSimpleCmp\Service;

use SimpleCMP\WpSimpleCmp\Database\Repositories\ConsentLogRepository;
use SimpleCMP\WpSimpleCmp\Database\Repositories\ConfigSnapshotRepository;

class VisitorAuskunftService
{
    private ConsentLogRepository $consentLogRepo;
    private ConfigSnapshotRepository $snapshotRepo;
    private VisitorUuidHasher $hasher;

    public function __construct()
    {
        global $wpdb;
        $this->consentLogRepo = new ConsentLogRepository($wpdb);
        $this->snapshotRepo = new ConfigSnapshotRepository($wpdb);
        $this->hasher = new VisitorUuidHasher();
    }

    public function query(string $site, string $rawUuid): array
    {
        $visitorHash = $this->hasher->hash($rawUuid);

        $decisions = $this->consentLogRepo->findByVisitor($site, $visitorHash);

        // Gather all referenced snapshots
        $snapshotHashes = [];
        foreach ($decisions as $d) {
            $snapshotHashes[$d->version_hash] = true;
        }

        $snapshots = [];
        foreach (array_keys($snapshotHashes) as $hash) {
            $snapshot = $this->snapshotRepo->find($site, $hash);
            if ($snapshot) {
                $snapshots[] = $snapshot;
            }
        }

        return [
            'visitor_hash' => $visitorHash,
            'decisions' => $decisions,
            'snapshots' => $snapshots,
        ];
    }
}
