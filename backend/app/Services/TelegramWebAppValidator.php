<?php

namespace App\Services;

class TelegramWebAppValidator
{
    /**
     * @return array<string, mixed>|null Parsed fields including 'user' as array if present
     */
    public function validateAndParse(string $initData, string $botToken): ?array
    {
        $initData = trim($initData);
        if (str_starts_with($initData, 'tma ')) {
            $initData = substr($initData, 4);
        }

        parse_str($initData, $data);
        $hash = $data['hash'] ?? null;
        if (! is_string($hash) || $hash === '') {
            return null;
        }

        unset($data['hash']);
        ksort($data);
        $pairs = [];
        foreach ($data as $k => $v) {
            $pairs[] = $k.'='.$v;
        }
        $dataCheckString = implode("\n", $pairs);

        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
        $calculated = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (! hash_equals($calculated, $hash)) {
            return null;
        }

        if (isset($data['user']) && is_string($data['user'])) {
            $decoded = json_decode($data['user'], true);
            $data['user'] = is_array($decoded) ? $decoded : null;
        }

        return $data;
    }
}
