<?php

namespace App\Services;

use App\Models\OrderMessageTemplate;

class OrderMessageService
{
    public function render(string $key, array $replacements, string $fallback): string
    {
        $tpl = OrderMessageTemplate::query()
            ->where('key', $key)
            ->where('active', true)
            ->first();

        $text = $tpl ? (string) $tpl->body : $fallback;
        foreach ($replacements as $k => $v) {
            $text = str_replace('{'.$k.'}', (string) $v, $text);
        }

        return $text;
    }
}

