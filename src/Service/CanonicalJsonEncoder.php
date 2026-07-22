<?php

namespace SimpleCMP\WpSimpleCmp\Service;

class CanonicalJsonEncoder
{
    /**
     * Encode an array to deterministic JSON (sorted keys, consistent encoding).
     */
    public function encode(array $data): string
    {
        return json_encode($this->deepSort($data), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function deepSort(mixed $data): mixed
    {
        if (is_array($data) && !$this->isSequential($data)) {
            ksort($data, SORT_STRING);
            foreach ($data as $key => $value) {
                $data[$key] = $this->deepSort($value);
            }
        } elseif (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->deepSort($value);
            }
        }

        return $data;
    }

    private function isSequential(array $arr): bool
    {
        if (empty($arr)) return true;
        return array_keys($arr) === range(0, count($arr) - 1);
    }
}
