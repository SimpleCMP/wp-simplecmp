<?php

namespace SimpleCMP\WpSimpleCmp\Database\Repositories;

class TranslationOverrideRepository extends AbstractRepository
{
    protected function tableName(): string
    {
        return 'simplecmp_translation_overrides';
    }

    public function findBySite(string $site): ?object
    {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM $this->table WHERE site = %s", $site),
            OBJECT
        ) ?: null;
    }

    public function upsert(string $site, string $overrides): void
    {
        $existing = $this->findBySite($site);
        if ($existing) {
            $this->wpdb->update($this->table, ['overrides' => $overrides, 'updated_at' => current_time('mysql')], ['site' => $site]);
        } else {
            $this->wpdb->insert($this->table, ['site' => $site, 'overrides' => $overrides, 'created_at' => current_time('mysql')]);
        }
    }

    public function getOverrides(string $site): ?string
    {
        $row = $this->findBySite($site);
        return $row ? $row->overrides : null;
    }
}
