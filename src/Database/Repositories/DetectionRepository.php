<?php

namespace SimpleCMP\WpSimpleCmp\Database\Repositories;

class DetectionRepository extends AbstractRepository
{
    protected function tableName(): string
    {
        return 'simplecmp_detections';
    }

    public function findById(int $id): ?object
    {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM $this->table WHERE id = %d", $id),
            OBJECT
        ) ?: null;
    }

    public function upsert(array $data): void
    {
        $source = $data['source'] ?? '';
        $kind = $data['kind'] ?? '';
        $identifier = $data['identifier'] ?? '';

        $existing = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT id, occurrences FROM $this->table WHERE source = %s AND kind = %s AND identifier = %s",
                $source, $kind, $identifier
            ),
            OBJECT
        );

        if ($existing) {
            $this->wpdb->update(
                $this->table,
                [
                    'occurrences' => $existing->occurrences + 1,
                    'last_seen' => time(),
                    'updated_at' => current_time('mysql'),
                    'received_at' => time(),
                    'payload' => $data['payload'] ?? null,
                    'first_seen_on' => $data['first_seen_on'] ?? null,
                    'page_url' => $data['page_url'] ?? null,
                ],
                ['id' => $existing->id]
            );
        } else {
            $insert = array_merge([
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
                'occurrences' => 1,
                'first_seen' => time(),
                'last_seen' => time(),
                'received_at' => time(),
                'dismissed_at' => 0,
            ], $data);

            $this->wpdb->insert($this->table, $insert);
        }
    }

    /**
     * @return object[]
     */
    public function findAll(array $args = []): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($args['source'])) {
            $where[] = 'source = %s';
            $params[] = $args['source'];
        }

        if (!empty($args['kind'])) {
            $where[] = 'kind = %s';
            $params[] = $args['kind'];
        }

        if (isset($args['dismissed'])) {
            if ($args['dismissed']) {
                $where[] = 'dismissed_at > 0';
            } else {
                $where[] = 'dismissed_at = 0';
            }
        }

        $whereClause = implode(' AND ', $where);

        $allowedOrderBy = ['last_seen', 'created_at', 'updated_at', 'identifier', 'kind', 'occurrences', 'first_seen', 'received_at', 'id'];
        $orderBy = in_array($args['orderby'] ?? 'last_seen', $allowedOrderBy, true) ? $args['orderby'] : 'last_seen';
        $order = strtoupper($args['order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

        $limit = (int) ($args['limit'] ?? 50);
        $offset = (int) ($args['offset'] ?? 0);

        $sql = "SELECT * FROM $this->table WHERE $whereClause ORDER BY $orderBy $order LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;

        $results = $this->wpdb->get_results(
            $this->wpdb->prepare($sql, $params),
            OBJECT
        );

        return $results ?: [];
    }

    public function countAll(array $args = []): int
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($args['source'])) {
            $where[] = 'source = %s';
            $params[] = $args['source'];
        }
        if (!empty($args['dismissed'])) {
            $where[] = 'dismissed_at = 0';
        }

        $whereClause = implode(' AND ', $where);

        $query = "SELECT COUNT(*) FROM $this->table WHERE $whereClause";
        if (!empty($params)) {
            $query = $this->wpdb->prepare($query, $params);
        }

        return (int) $this->wpdb->get_var($query);
    }

    public function dismiss(int $id): void
    {
        $this->wpdb->update($this->table, ['dismissed_at' => time()], ['id' => $id]);
    }

    public function undismiss(int $id): void
    {
        $this->wpdb->update($this->table, ['dismissed_at' => 0], ['id' => $id]);
    }

    public function purge(int $id): void
    {
        $this->wpdb->delete($this->table, ['id' => $id]);
    }

    public function bulkDismiss(array $ids): void
    {
        if (empty($ids)) return;
        $placeholders = implode(',', array_fill(0, count($ids), '%d'));
        $this->wpdb->query(
            $this->wpdb->prepare(
                "UPDATE $this->table SET dismissed_at = %d WHERE id IN ($placeholders)",
                array_merge([time()], $ids)
            )
        );
    }

    public function bulkPurge(array $ids): void
    {
        if (empty($ids)) return;
        $placeholders = implode(',', array_fill(0, count($ids), '%d'));
        $this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM $this->table WHERE id IN ($placeholders)",
                $ids
            )
        );
    }

    public function dismissAll(): void
    {
        $this->wpdb->query(
            $this->wpdb->prepare(
                "UPDATE $this->table SET dismissed_at = %d WHERE dismissed_at = 0",
                time()
            )
        );
    }

    public function findByTriple(string $source, string $kind, string $identifier): ?object
    {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM $this->table WHERE source = %s AND kind = %s AND identifier = %s",
                $source, $kind, $identifier
            ),
            OBJECT
        ) ?: null;
    }
}
