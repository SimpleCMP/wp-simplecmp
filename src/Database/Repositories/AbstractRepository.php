<?php

namespace SimpleCMP\WpSimpleCmp\Database\Repositories;

abstract class AbstractRepository
{
    protected \wpdb $wpdb;
    protected string $table;

    public function __construct(\wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . $this->tableName();
    }

    abstract protected function tableName(): string;

    protected function prepareTable(): string
    {
        return $this->wpdb->prefix . $this->tableName();
    }
}
