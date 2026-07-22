<?php

namespace SimpleCMP\WpSimpleCmp\Database\Repositories;

class ConsentLogRepository extends AbstractRepository
{
    protected function tableName(): string
    {
        return 'simplecmp_consent_logs';
    }

    public function upsert(array $data): bool
    {
        $existing = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT id FROM $this->table
                 WHERE site = %s AND visitor_id_sha256 = %s AND version_hash = %s AND decision_hash = %s",
                $data['site'], $data['visitor_id_sha256'], $data['version_hash'], $data['decision_hash']
            ),
            OBJECT
        );

        if ($existing) {
            return false; // Deduplicated
        }

        $data['created_at'] = current_time('mysql');
        $this->wpdb->insert($this->table, $data);
        return true;
    }

    /**
     * @return object[]
     */
    public function findByVisitor(string $site, string $visitorIdSha256): array
    {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM $this->table WHERE site = %s AND visitor_id_sha256 = %s ORDER BY created_at DESC",
                $site, $visitorIdSha256
            ),
            OBJECT
        ) ?: [];
    }

    /**
     * @return object[]
     */
    public function findByHash(string $versionHash, int $limit = 100): array
    {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM $this->table WHERE version_hash = %s ORDER BY created_at DESC LIMIT %d",
                $versionHash, $limit
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

    public function deleteBySite(string $site): int
    {
        return (int) $this->wpdb->query(
            $this->wpdb->prepare("DELETE FROM $this->table WHERE site = %s", $site)
        );
    }

    public function deleteOlderThan(int $timestamp): int
    {
        return (int) $this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM $this->table WHERE UNIX_TIMESTAMP(created_at) < %d",
                $timestamp
            )
        );
    }
}
