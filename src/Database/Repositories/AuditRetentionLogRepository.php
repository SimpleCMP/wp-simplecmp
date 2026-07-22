<?php

namespace SimpleCMP\WpSimpleCmp\Database\Repositories;

class AuditRetentionLogRepository extends AbstractRepository
{
    protected function tableName(): string
    {
        return 'simplecmp_audit_retention_logs';
    }

    public function log(array $data): int
    {
        $data['created_at'] = current_time('mysql');
        $this->wpdb->insert($this->table, $data);
        return $this->wpdb->insert_id;
    }

    /**
     * @return object[]
     */
    public function findAll(int $limit = 100): array
    {
        return $this->wpdb->get_results(
            "SELECT * FROM $this->table ORDER BY created_at DESC LIMIT " . (int) $limit,
            OBJECT
        ) ?: [];
    }
}
