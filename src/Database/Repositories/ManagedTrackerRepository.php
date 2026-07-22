<?php

namespace SimpleCMP\WpSimpleCmp\Database\Repositories;

class ManagedTrackerRepository extends AbstractRepository
{
    protected function tableName(): string
    {
        return 'simplecmp_managed_trackers';
    }

    /**
     * @return object[]
     */
    public function findBySite(string $site): array
    {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM $this->table WHERE site = %s AND deleted = 0",
                $site
            ),
            OBJECT
        ) ?: [];
    }

    public function insert(array $data): int
    {
        $data['created_at'] = current_time('mysql');
        $data['updated_at'] = current_time('mysql');
        $this->wpdb->insert($this->table, $data);
        return $this->wpdb->insert_id;
    }

    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = current_time('mysql');
        return (bool) $this->wpdb->update($this->table, $data, ['id' => $id]);
    }

    public function softDelete(int $id): bool
    {
        return (bool) $this->wpdb->update($this->table, ['deleted' => 1], ['id' => $id]);
    }
}
