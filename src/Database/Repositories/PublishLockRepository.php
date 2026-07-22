<?php

namespace SimpleCMP\WpSimpleCmp\Database\Repositories;

class PublishLockRepository extends AbstractRepository
{
    protected function tableName(): string
    {
        return 'simplecmp_publish_locks';
    }

    public function acquire(string $scope, int $userId): bool
    {
        $existing = $this->getLock($scope);
        $now = time();

        if ($existing) {
            // Lock expired (> 30 min inactivity)? Take over.
            if ($existing->last_activity_at < ($now - 1800)) {
                $this->wpdb->update($this->table, [
                    'owner_be_user' => $userId,
                    'acquired_at' => $now,
                    'last_activity_at' => $now,
                ], ['scope' => $scope]);
                return true;
            }
            // Same user? Touch.
            if ((int) $existing->owner_be_user === $userId) {
                $this->touch($scope);
                return true;
            }
            return false;
        }

        $this->wpdb->insert($this->table, [
            'scope' => $scope,
            'owner_be_user' => $userId,
            'acquired_at' => $now,
            'last_activity_at' => $now,
            'created_at' => current_time('mysql'),
        ]);
        return true;
    }

    public function release(string $scope): void
    {
        $this->wpdb->delete($this->table, ['scope' => $scope]);
    }

    public function touch(string $scope): void
    {
        $this->wpdb->update($this->table, ['last_activity_at' => time()], ['scope' => $scope]);
    }

    public function getLock(string $scope): ?object
    {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM $this->table WHERE scope = %s", $scope),
            OBJECT
        ) ?: null;
    }

    public function isLocked(string $scope): bool
    {
        $lock = $this->getLock($scope);
        if (!$lock) return false;
        return $lock->last_activity_at >= (time() - 1800);
    }
}
