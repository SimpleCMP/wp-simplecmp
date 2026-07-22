<?php

namespace SimpleCMP\WpSimpleCmp\Database\Repositories;

class LibraryCacheRepository extends AbstractRepository
{
    protected function tableName(): string
    {
        return 'simplecmp_library_cache';
    }

    public function find(string $queryType, string $queryValue): ?object
    {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM $this->table WHERE query_type = %s AND query_value = %s AND expires_at > %d",
                $queryType, $queryValue, time()
            ),
            OBJECT
        ) ?: null;
    }

    public function upsert(string $queryType, string $queryValue, string $responseJson, int $ttl): void
    {
        $now = time();
        $this->wpdb->query(
            $this->wpdb->prepare(
                "INSERT INTO $this->table (query_type, query_value, response_json, fetched_at, expires_at, created_at, updated_at)
                 VALUES (%s, %s, %s, %d, %d, %s, %s)
                 ON DUPLICATE KEY UPDATE response_json = VALUES(response_json), fetched_at = VALUES(fetched_at), expires_at = VALUES(expires_at), updated_at = VALUES(updated_at)",
                $queryType, $queryValue, $responseJson, $now, $now + $ttl,
                current_time('mysql'), current_time('mysql')
            )
        );
    }
}
