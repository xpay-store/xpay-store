<?php

namespace Bots;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Telegram @XPayStoreAppBot webhook handler (mounted in routes/api.php).
 */
final class StoreBotWebhook
{
    public function __invoke(Request $request, string $secret): JsonResponse|Response
    {
        $expected = (string) config('services.telegram.store_webhook_secret');
        if ($expected === '' || ! hash_equals($expected, $secret)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $update = $request->all();
        $message = $update['message'] ?? null;
        if (! is_array($message)) {
            return response()->json(['ok' => true]);
        }

        $text = isset($message['text']) ? (string) $message['text'] : '';
        $chat = $message['chat'] ?? null;
        $chatId = is_array($chat) ? ($chat['id'] ?? null) : null;

        if ($chatId === null) {
            return response()->json(['ok' => true]);
        }

        if (str_starts_with($text, '/start')) {
            $this->sendStoreOpenButton((string) $chatId);
        }

        return response()->json(['ok' => true]);
    }

    private function sendStoreOpenButton(string $chatId): void
    {
        $token = (string) config('services.telegram.store_bot_token');
        if ($token === '') {
            return;
        }

        $url = rtrim((string) config('app.frontend_url'), '/');
        if ($url === '') {
            $url = rtrim((string) config('app.url'), '/');
        }

        $keyboard = [
            'keyboard' => [
                [
                    [
                        'text' => 'Open Store',
                        'web_app' => ['url' => $url],
                    ],
                ],
            ],
            'resize_keyboard' => true,
        ];

        $client = new \GuzzleHttp\Client(['base_uri' => 'https://api.telegram.org', 'timeout' => 20]);
        try {
            $client->post('/bot'.$token.'/sendMessage', [
                'json' => [
                    'chat_id' => $chatId,
                    'text' => 'مرحباً بك في XPayStore. اضغط لفتح المتجر:',
                    'reply_markup' => json_encode($keyboard, JSON_UNESCAPED_UNICODE),
                ],
            ]);
        } catch (\Throwable) {
            //
        }
    }
}
