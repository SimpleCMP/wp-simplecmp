<?php

namespace SimpleCMP\WpSimpleCmp\Core;

use SimpleCMP\WpSimpleCmp\Database\Schema;

class Activator
{
    public static function activate(): void
    {
        global $wpdb;

        $prefix = $wpdb->prefix;
        $charsetCollate = $wpdb->get_charset_collate();

        self::createTable("{$prefix}simplecmp_services", "
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            service_id varchar(100) NOT NULL DEFAULT '',
            name varchar(255) NOT NULL DEFAULT '',
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
            library_adopted_at bigint(20) unsigned NOT NULL DEFAULT 0,
            status varchar(20) NOT NULL DEFAULT 'published',
            PRIMARY KEY  (id),
            UNIQUE KEY service_id (service_id),
            KEY library_adopted_at (library_adopted_at)
        ", $charsetCollate);

        self::createTable("{$prefix}simplecmp_themes", "
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            site varchar(100) NOT NULL DEFAULT '',
            tokens longtext,
            PRIMARY KEY  (id),
            UNIQUE KEY site (site)
        ", $charsetCollate);

        self::createTable("{$prefix}simplecmp_translation_overrides", "
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            site varchar(100) NOT NULL DEFAULT '',
            overrides longtext,
            PRIMARY KEY  (id),
            UNIQUE KEY site (site)
        ", $charsetCollate);

        self::createTable("{$prefix}simplecmp_managed_trackers", "
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            site varchar(100) NOT NULL DEFAULT '',
            tracker_type varchar(50) NOT NULL DEFAULT '',
            service_id varchar(100) NOT NULL DEFAULT '',
            config longtext,
            deleted tinyint(1) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            KEY site (site),
            KEY tracker_type (tracker_type),
            KEY deleted (deleted)
        ", $charsetCollate);

        self::createTable("{$prefix}simplecmp_library_cache", "
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            query_type varchar(16) NOT NULL DEFAULT '',
            query_value varchar(255) NOT NULL DEFAULT '',
            response_json longtext,
            fetched_at bigint(20) unsigned NOT NULL DEFAULT 0,
            expires_at bigint(20) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            UNIQUE KEY query_key (query_type, query_value),
            KEY expires_at (expires_at)
        ", $charsetCollate);

        self::createTable("{$prefix}simplecmp_detections", "
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            source varchar(100) NOT NULL DEFAULT '',
            kind varchar(32) NOT NULL DEFAULT '',
            identifier varchar(500) NOT NULL DEFAULT '',
            origin varchar(255) DEFAULT NULL,
            page_url varchar(500) DEFAULT NULL,
            first_seen_on varchar(500) DEFAULT NULL,
            sent_at varchar(40) DEFAULT NULL,
            first_seen bigint(20) DEFAULT NULL,
            last_seen bigint(20) DEFAULT NULL,
            received_at bigint(20) unsigned NOT NULL DEFAULT 0,
            occurrences int(11) unsigned NOT NULL DEFAULT 1,
            library_version varchar(40) DEFAULT NULL,
            user_agent varchar(500) DEFAULT NULL,
            referrer varchar(500) DEFAULT NULL,
            payload longtext,
            dismissed_at bigint(20) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            UNIQUE KEY dedup_key (source, kind, identifier),
            KEY received_at (received_at),
            KEY dismissed_at (dismissed_at)
        ", $charsetCollate);

        self::createTable("{$prefix}simplecmp_allowed_stylesheet_hosts", "
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            source varchar(100) NOT NULL DEFAULT '',
            host varchar(255) NOT NULL DEFAULT '',
            PRIMARY KEY  (id),
            UNIQUE KEY source_host (source, host)
        ", $charsetCollate);

        self::createTable("{$prefix}simplecmp_config_snapshots", "
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            site varchar(64) NOT NULL DEFAULT '',
            version_hash char(64) NOT NULL DEFAULT '',
            canonical_json longtext,
            trigger_event varchar(64) NOT NULL DEFAULT '',
            creator_be_user bigint(20) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            UNIQUE KEY site_version (site, version_hash),
            KEY by_site_date (site, created_at),
            KEY version (version_hash)
        ", $charsetCollate);

        self::createTable("{$prefix}simplecmp_consent_logs", "
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
        ", $charsetCollate);

        self::createTable("{$prefix}simplecmp_audit_retention_logs", "
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            target_table varchar(64) NOT NULL DEFAULT '',
            target_site varchar(64) NOT NULL DEFAULT '',
            rows_deleted int(11) unsigned NOT NULL DEFAULT 0,
            keep_days int(11) unsigned NOT NULL DEFAULT 0,
            oldest_kept_crdate bigint(20) unsigned NOT NULL DEFAULT 0,
            invoked_by varchar(64) NOT NULL DEFAULT '',
            invocation_reason text,
            dry_run tinyint(1) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            KEY by_table_date (target_table, created_at),
            KEY by_date (created_at)
        ", $charsetCollate);

        self::createTable("{$prefix}simplecmp_active_settings", "
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            site varchar(64) NOT NULL DEFAULT '',
            active_json longtext,
            last_modified_be_user bigint(20) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            UNIQUE KEY site (site)
        ", $charsetCollate);

        self::createTable("{$prefix}simplecmp_publish_locks", "
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            scope varchar(64) NOT NULL DEFAULT '',
            owner_be_user bigint(20) unsigned NOT NULL DEFAULT 0,
            acquired_at bigint(20) unsigned NOT NULL DEFAULT 0,
            last_activity_at bigint(20) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            UNIQUE KEY scope (scope),
            KEY owner_be_user (owner_be_user),
            KEY last_activity_at (last_activity_at)
        ", $charsetCollate);

        add_option('wp_simplecmp_version', WP_SIMPLECMP_VERSION);

        // Generate bridge secret if not configured and not in env
        if (!get_option('simplecmp_bridge_secret') && !defined('SIMPLECMP_BRIDGE_SECRET')) {
            update_option('simplecmp_bridge_secret', bin2hex(random_bytes(32)));
        }
    }

    private static function createTable(string $tableName, string $columns, string $charsetCollate): void
    {
        global $wpdb;

        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` ($columns) $charsetCollate";
        $wpdb->query($sql);
    }
}
