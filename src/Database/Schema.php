<?php

namespace SimpleCMP\WpSimpleCmp\Database;

class Schema
{
    private \wpdb $wpdb;
    private array $tables = [];

    public function __construct(\wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    public function createTables(): void
    {
        $prefix = $this->wpdb->prefix;

        $this->addServiceTable($prefix);
        $this->addThemeTable($prefix);
        $this->addTranslationOverrideTable($prefix);
        $this->addManagedTrackerTable($prefix);
        $this->addLibraryCacheTable($prefix);
        $this->addDetectionTable($prefix);
        $this->addAllowedStylesheetHostTable($prefix);
        $this->addConfigSnapshotTable($prefix);
        $this->addConsentLogTable($prefix);
        $this->addAuditRetentionLogTable($prefix);
        $this->addActiveSettingsTable($prefix);
        $this->addPublishLockTable($prefix);
    }

    private function addServiceTable(string $prefix): void
    {
        $table = $prefix . 'simplecmp_services';
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL auto_increment,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            service_id varchar(100) DEFAULT '' NOT NULL,
            name varchar(255) DEFAULT '' NOT NULL,
            vendor varchar(255) DEFAULT NULL,
            vendor_country varchar(8) DEFAULT NULL,
            vendor_address text,
            vendor_opt_out_url varchar(500) DEFAULT NULL,
            vendor_partner text,
            vendor_description text,
            purposes longtext,
            privacy_policy_url varchar(500) DEFAULT NULL,
            description longtext,
            retention longtext,
            i18n longtext,
            cookies longtext,
            origins longtext,
            extensions longtext,
            placeholder_title varchar(255) DEFAULT NULL,
            placeholder_description text,
            library_adopted_at bigint(20) unsigned DEFAULT 0 NOT NULL,
            status varchar(20) DEFAULT 'published' NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY service_id (service_id),
            KEY library_adopted_at (library_adopted_at)
        )";
        $this->tables[] = $sql;
    }

    private function addThemeTable(string $prefix): void
    {
        $table = $prefix . 'simplecmp_themes';
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL auto_increment,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            site varchar(100) DEFAULT '' NOT NULL,
            tokens longtext,
            PRIMARY KEY  (id),
            UNIQUE KEY site (site)
        )";
        $this->tables[] = $sql;
    }

    private function addTranslationOverrideTable(string $prefix): void
    {
        $table = $prefix . 'simplecmp_translation_overrides';
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL auto_increment,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            site varchar(100) DEFAULT '' NOT NULL,
            overrides longtext,
            PRIMARY KEY  (id),
            UNIQUE KEY site (site)
        )";
        $this->tables[] = $sql;
    }

    private function addManagedTrackerTable(string $prefix): void
    {
        $table = $prefix . 'simplecmp_managed_trackers';
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL auto_increment,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            site varchar(100) DEFAULT '' NOT NULL,
            tracker_type varchar(50) DEFAULT '' NOT NULL,
            service_id varchar(100) DEFAULT '' NOT NULL,
            config longtext,
            deleted tinyint(1) unsigned DEFAULT 0 NOT NULL,
            PRIMARY KEY  (id),
            KEY site (site),
            KEY tracker_type (tracker_type),
            KEY deleted (deleted)
        )";
        $this->tables[] = $sql;
    }

    private function addLibraryCacheTable(string $prefix): void
    {
        $table = $prefix . 'simplecmp_library_cache';
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL auto_increment,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            query_type varchar(16) DEFAULT '' NOT NULL,
            query_value varchar(255) DEFAULT '' NOT NULL,
            response_json longtext,
            fetched_at bigint(20) unsigned DEFAULT 0 NOT NULL,
            expires_at bigint(20) unsigned DEFAULT 0 NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY query_key (query_type, query_value),
            KEY expires_at (expires_at)
        )";
        $this->tables[] = $sql;
    }

    private function addDetectionTable(string $prefix): void
    {
        $table = $prefix . 'simplecmp_detections';
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL auto_increment,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            source varchar(100) DEFAULT '' NOT NULL,
            kind varchar(32) DEFAULT '' NOT NULL,
            identifier varchar(500) DEFAULT '' NOT NULL,
            origin varchar(255) DEFAULT NULL,
            page_url varchar(500) DEFAULT NULL,
            first_seen_on varchar(500) DEFAULT NULL,
            sent_at varchar(40) DEFAULT NULL,
            first_seen bigint(20) DEFAULT NULL,
            last_seen bigint(20) DEFAULT NULL,
            received_at bigint(20) unsigned DEFAULT 0 NOT NULL,
            occurrences int(11) unsigned DEFAULT 1 NOT NULL,
            library_version varchar(40) DEFAULT NULL,
            user_agent varchar(500) DEFAULT NULL,
            referrer varchar(500) DEFAULT NULL,
            payload longtext,
            dismissed_at bigint(20) unsigned DEFAULT 0 NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY dedup_key (source, kind, identifier),
            KEY received_at (received_at),
            KEY dismissed_at (dismissed_at)
        )";
        $this->tables[] = $sql;
    }

    private function addAllowedStylesheetHostTable(string $prefix): void
    {
        $table = $prefix . 'simplecmp_allowed_stylesheet_hosts';
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL auto_increment,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            source varchar(100) DEFAULT '' NOT NULL,
            host varchar(255) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY source_host (source, host)
        )";
        $this->tables[] = $sql;
    }

    private function addConfigSnapshotTable(string $prefix): void
    {
        $table = $prefix . 'simplecmp_config_snapshots';
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL auto_increment,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            site varchar(64) DEFAULT '' NOT NULL,
            version_hash char(64) DEFAULT '' NOT NULL,
            canonical_json longtext,
            trigger_event varchar(64) DEFAULT '' NOT NULL,
            creator_be_user bigint(20) unsigned DEFAULT 0 NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY site_version (site, version_hash),
            KEY by_site_date (site, created_at),
            KEY version (version_hash)
        )";
        $this->tables[] = $sql;
    }

    private function addConsentLogTable(string $prefix): void
    {
        $table = $prefix . 'simplecmp_consent_logs';
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL auto_increment,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            site varchar(64) NOT NULL DEFAULT '',
            version_hash char(64) NOT NULL DEFAULT '',
            visitor_id_sha256 char(64) NOT NULL DEFAULT '',
            decision_hash char(64) NOT NULL DEFAULT '',
            decisions_json text,
            decision_type varchar(32) NOT NULL DEFAULT '',
            ua_family varchar(32) DEFAULT NULL,
            page_url_host varchar(255) DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY visitor_version_decision (site, visitor_id_sha256, version_hash, decision_hash),
            KEY by_version (version_hash),
            KEY by_visitor (visitor_id_sha256, site),
            KEY by_date (created_at),
            KEY by_site_date (site, created_at)
        )";
        $this->tables[] = $sql;
    }

    private function addAuditRetentionLogTable(string $prefix): void
    {
        $table = $prefix . 'simplecmp_audit_retention_logs';
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL auto_increment,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            target_table varchar(64) DEFAULT '' NOT NULL,
            target_site varchar(64) DEFAULT '' NOT NULL,
            rows_deleted int(11) unsigned DEFAULT 0 NOT NULL,
            keep_days int(11) unsigned DEFAULT 0 NOT NULL,
            oldest_kept_crdate bigint(20) unsigned DEFAULT 0 NOT NULL,
            invoked_by varchar(64) DEFAULT '' NOT NULL,
            invocation_reason text,
            dry_run tinyint(1) unsigned DEFAULT 0 NOT NULL,
            PRIMARY KEY  (id),
            KEY by_table_date (target_table, created_at),
            KEY by_date (created_at)
        )";
        $this->tables[] = $sql;
    }

    private function addActiveSettingsTable(string $prefix): void
    {
        $table = $prefix . 'simplecmp_active_settings';
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL auto_increment,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            site varchar(64) DEFAULT '' NOT NULL,
            active_json longtext,
            last_modified_be_user bigint(20) unsigned DEFAULT 0 NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY site (site)
        )";
        $this->tables[] = $sql;
    }

    private function addPublishLockTable(string $prefix): void
    {
        $table = $prefix . 'simplecmp_publish_locks';
        $sql = "CREATE TABLE $table (
            id bigint(20) unsigned NOT NULL auto_increment,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            scope varchar(64) NOT NULL DEFAULT '',
            owner_be_user bigint(20) unsigned NOT NULL DEFAULT 0,
            acquired_at bigint(20) unsigned NOT NULL DEFAULT 0,
            last_activity_at bigint(20) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            UNIQUE KEY scope (scope),
            KEY owner_be_user (owner_be_user),
            KEY last_activity_at (last_activity_at)
        )";
        $this->tables[] = $sql;
    }

    public function toSql(string $charsetCollate = ''): string
    {
        return implode("\n\n", $this->tables);
    }

    /**
     * @return string[]
     */
    public function toArray(string $charsetCollate = ''): array
    {
        return $this->tables;
    }
}
