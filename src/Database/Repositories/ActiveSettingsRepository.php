<?php

namespace SimpleCMP\WpSimpleCmp\Database\Repositories;

class ActiveSettingsRepository extends AbstractRepository
{
    protected function tableName(): string
    {
        return 'simplecmp_active_settings';
    }

    public function findBySite(string $site): ?object
    {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM $this->table WHERE site = %s", $site),
            OBJECT
        ) ?: null;
    }

    public function getActiveJson(string $site): ?string
    {
        $row = $this->findBySite($site);
        return $row ? $row->active_json : null;
    }

    public function upsert(string $site, string $activeJson, int $userId): void
    {
        $existing = $this->findBySite($site);
        if ($existing) {
            $this->wpdb->update($this->table, [
                'active_json' => $activeJson,
                'last_modified_be_user' => $userId,
                'updated_at' => current_time('mysql'),
            ], ['site' => $site]);
        } else {
            $this->wpdb->insert($this->table, [
                'site' => $site,
                'active_json' => $activeJson,
                'last_modified_be_user' => $userId,
                'created_at' => current_time('mysql'),
            ]);
        }
    }

    /**
     * @return string[]
     */
    public function getAllSites(): array
    {
        $results = $this->wpdb->get_col("SELECT DISTINCT site FROM $this->table");
        return $results ?: [];
    }
}
