<?php

namespace SimpleCMP\WpSimpleCmp\Service;

class WebhookPayloadValidator
{
    public function validate(array $payload): array
    {
        $errors = [];

        if (!isset($payload['detections']) || !is_array($payload['detections'])) {
            $errors[] = 'Missing or invalid "detections" array';
            return $errors;
        }

        foreach ($payload['detections'] as $i => $detection) {
            if (!is_array($detection)) {
                $errors[] = "detections[$i] is not an object";
                continue;
            }

            if (empty($detection['source'] ?? '')) {
                $errors[] = "detections[$i].source is required";
            }

            if (empty($detection['kind'] ?? '')) {
                $errors[] = "detections[$i].kind is required";
            }

            if (empty($detection['identifier'] ?? '')) {
                $errors[] = "detections[$i].identifier is required";
            }

            if (isset($detection['kind']) && !in_array($detection['kind'], ['cookie', 'origin', 'localStorage', 'stylesheet'], true)) {
                $errors[] = "detections[$i].kind '{$detection['kind']}' is not valid";
            }
        }

        return $errors;
    }
}
