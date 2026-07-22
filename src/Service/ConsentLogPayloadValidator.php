<?php

namespace SimpleCMP\WpSimpleCmp\Service;

class ConsentLogPayloadValidator
{
    public function validate(array $payload): array
    {
        $errors = [];

        if (empty($payload['site'] ?? '')) {
            $errors[] = 'site is required';
        }

        if (empty($payload['version_hash'] ?? '')) {
            $errors[] = 'version_hash is required';
        } elseif (strlen($payload['version_hash']) !== 64) {
            $errors[] = 'version_hash must be 64 characters';
        }

        if (empty($payload['visitor_id_sha256'] ?? '')) {
            $errors[] = 'visitor_id_sha256 is required';
        } elseif (strlen($payload['visitor_id_sha256']) !== 64) {
            $errors[] = 'visitor_id_sha256 must be 64 characters';
        }

        if (empty($payload['decisions_json'] ?? '')) {
            $errors[] = 'decisions_json is required';
        }

        if (empty($payload['decision_type'] ?? '')) {
            $errors[] = 'decision_type is required';
        } elseif (!in_array($payload['decision_type'], ['accept', 'decline', 'save'], true)) {
            $errors[] = 'decision_type must be accept, decline, or save';
        }

        return $errors;
    }
}
