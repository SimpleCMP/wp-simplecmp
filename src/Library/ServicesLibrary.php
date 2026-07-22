<?php

namespace SimpleCMP\WpSimpleCmp\Library;

class ServicesLibrary
{
    private string $dataPath;
    private ?array $services = null;

    public function __construct()
    {
        $this->dataPath = WP_SIMPLECMP_DIR . 'data/services/';
    }

    public function dataPath(): string
    {
        return $this->dataPath;
    }

    /**
     * @return \Generator<array>
     */
    public function services(): \Generator
    {
        $files = glob($this->dataPath . '*.json');
        if (!$files) return;

        foreach ($files as $file) {
            $json = json_decode(file_get_contents($file), true);
            if ($json) {
                yield $json;
            }
        }
    }

    /**
     * @return array[]
     */
    public function all(): array
    {
        if ($this->services === null) {
            $this->services = iterator_to_array($this->services());
        }
        return $this->services;
    }

    public function find(string $serviceId): ?array
    {
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $serviceId)) {
            return null;
        }
        $file = $this->dataPath . $serviceId . '.json';
        if (!file_exists($file)) return null;

        $json = json_decode(file_get_contents($file), true);
        return $json ?: null;
    }

    /**
     * @return array[]
     */
    public function findAll(array $serviceIds): array
    {
        $result = [];
        foreach ($serviceIds as $id) {
            $service = $this->find($id);
            if ($service) {
                $result[$id] = $service;
            }
        }
        return $result;
    }

    public function dataHash(): string
    {
        $hashes = [];
        $files = glob($this->dataPath . '*.json');
        if (!$files) return '';

        sort($files);
        foreach ($files as $file) {
            $hashes[] = sha1_file($file);
        }
        return sha1(implode('', $hashes));
    }

    public function fileCount(): int
    {
        return count(glob($this->dataPath . '*.json') ?: []);
    }

    /**
     * @return string[]
     */
    public function serviceIds(): array
    {
        $ids = [];
        $files = glob($this->dataPath . '*.json') ?: [];
        foreach ($files as $file) {
            $ids[] = basename($file, '.json');
        }
        sort($ids);
        return $ids;
    }
}
