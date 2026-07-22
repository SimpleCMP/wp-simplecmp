<?php

namespace SimpleCMP\WpSimpleCmp\Database\Repositories;

class ConfigSnapshotRepository extends AbstractRepository
{
    protected function tableName(): string
    {
        return 'simplecmp_config_snapshots';
    }

    public function find(string $site, string $versionHash): ?object
    {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM $this->table WHERE site = %s AND version_hash = %s",
                $site, $versionHash
            ),
            OBJECT
        ) ?: null;
    }

    /**
     * @return object[]
     */
    public function findBySite(string $site, int $limit = 50, int $offset = 0): array
    {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM $this->table WHERE site = %s ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $site, $limit, $offset
            ),
            OBJECT
        ) ?: [];
    }

    public function countBySite(string $site): int
    {
        return (int) $this->wpdb->get_var(
            $this->wpdb->prepare("SELECT COUNT(*) FROM $this->table WHERE site = %s", $site)
        );
    }

    public function insert(string $site, string $versionHash, string $canonicalJson, string $triggerEvent, int $userId): ?int
    {
        $existing = $this->find($site, $versionHash);
        if ($existing) {
            return null; // Deduplicated
        }

        $this->wpdb->insert($this->table, [
            'site' => $site,
            'version_hash' => $versionHash,
            'canonical_json' => $canonicalJson,
            'trigger_event' => $triggerEvent,
            'creator_be_user' => $userId,
            'created_at' => current_time('mysql'),
        ]);

        return $this->wpdb->insert_id;
    }

    public function findPrevious(string $site, string $versionHash): ?object
    {
        $current = $this->find($site, $versionHash);
        if (!$current) return null;

        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM $this->table WHERE site = %s AND created_at < %s ORDER BY created_at DESC LIMIT 1",
                $site, $current->created_at
            ),
            OBJECT
        ) ?: null;
    }

    public function deleteOlderThan(int $timestamp, string $site): int
    {
        return (int) $this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM $this->table WHERE site = %s AND UNIX_TIMESTAMP(created_at) < %d",
                $site, $timestamp
            )
        );
    }
}
