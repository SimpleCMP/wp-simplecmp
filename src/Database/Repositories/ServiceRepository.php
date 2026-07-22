<?php

namespace SimpleCMP\WpSimpleCmp\Database\Repositories;

class ServiceRepository extends AbstractRepository
{
    protected function tableName(): string
    {
        return 'simplecmp_services';
    }

    public function findById(int $id): ?object
    {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM $this->table WHERE id = %d", $id),
            OBJECT
        );
        return $row ?: null;
    }

    public function findByServiceId(string $serviceId): ?object
    {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM $this->table WHERE service_id = %s", $serviceId),
            OBJECT
        );
        return $row ?: null;
    }

    /**
     * @return object[]
     */
    public function findAll(): array
    {
        return $this->wpdb->get_results(
            "SELECT * FROM $this->table WHERE status = 'published' ORDER BY name ASC",
            OBJECT
        ) ?: [];
    }

    /**
     * @return object[]
     */
    public function findByOrigin(string $origin): array
    {
        $results = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM $this->table WHERE status = 'published' AND origins LIKE %s",
                '%' . $this->wpdb->esc_like($origin) . '%'
            ),
            OBJECT
        );
        return $results ?: [];
    }

    public function insert(array $data, string $status = 'published'): int
    {
        $data['status'] = $status;
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

    public function updateByServiceId(string $serviceId, array $data): bool
    {
        $data['updated_at'] = current_time('mysql');
        return (bool) $this->wpdb->update($this->table, $data, ['service_id' => $serviceId]);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->wpdb->delete($this->table, ['id' => $id]);
    }

    public function countAll(): int
    {
        return (int) $this->wpdb->get_var(
            "SELECT COUNT(*) FROM $this->table WHERE status = 'published'"
        );
    }
}
