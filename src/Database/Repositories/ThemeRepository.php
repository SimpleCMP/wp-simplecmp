<?php

namespace SimpleCMP\WpSimpleCmp\Database\Repositories;

class ThemeRepository extends AbstractRepository
{
    protected function tableName(): string
    {
        return 'simplecmp_themes';
    }

    public function findBySite(string $site): ?object
    {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM $this->table WHERE site = %s", $site),
            OBJECT
        ) ?: null;
    }

    public function upsert(string $site, string $tokens): void
    {
        $existing = $this->findBySite($site);
        if ($existing) {
            $this->wpdb->update($this->table, ['tokens' => $tokens, 'updated_at' => current_time('mysql')], ['site' => $site]);
        } else {
            $this->wpdb->insert($this->table, ['site' => $site, 'tokens' => $tokens, 'created_at' => current_time('mysql')]);
        }
    }

    public function getTokens(string $site): ?string
    {
        $row = $this->findBySite($site);
        return $row ? $row->tokens : null;
    }

    public function deleteBySite(string $site): void
    {
        $this->wpdb->delete($this->table, ['site' => $site]);
    }
}
