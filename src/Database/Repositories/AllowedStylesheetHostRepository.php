<?php

namespace SimpleCMP\WpSimpleCmp\Database\Repositories;

class AllowedStylesheetHostRepository extends AbstractRepository
{
    protected function tableName(): string
    {
        return 'simplecmp_allowed_stylesheet_hosts';
    }

    /**
     * @return object[]
     */
    public function findBySource(string $source): array
    {
        return $this->wpdb->get_results(
            $this->wpdb->prepare("SELECT * FROM $this->table WHERE source = %s", $source),
            OBJECT
        ) ?: [];
    }

    public function exists(string $source, string $host): bool
    {
        $count = $this->wpdb->get_var(
            $this->wpdb->prepare("SELECT COUNT(*) FROM $this->table WHERE source = %s AND host = %s", $source, $host)
        );
        return (int) $count > 0;
    }

    public function insert(string $source, string $host): int
    {
        $this->wpdb->insert($this->table, [
            'source' => $source,
            'host' => $host,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        ]);
        return $this->wpdb->insert_id;
    }

    public function delete(string $source, string $host): bool
    {
        return (bool) $this->wpdb->delete($this->table, ['source' => $source, 'host' => $host]);
    }
}
