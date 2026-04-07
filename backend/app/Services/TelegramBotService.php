<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    private Client $http;

    public function __construct(?Client $http = null)
    {
        $this->http = $http ?? new Client([
            'base_uri' => 'https://api.telegram.org',
            'timeout' => 20,
        ]);
    }

    public function sendDepositGroupNotification(
        string $depositId,
        float $amountUsd,
        float $amountSyp,
        string $currency,
        string $method,
        string $userLabel,
        string $proofUrl,
        string $transactionId
    ): bool {
        $token = config('services.telegram.deposit_bot_token');
        $chatId = config('services.telegram.admin_group_id');
        if (! is_string($token) || $token === '' || ! is_string($chatId) || $chatId === '') {
            Log::warning('telegram.deposit_bot: missing token or admin group id');

            return false;
        }

        $safeUser = str_replace(["\n", "\r"], ' ', $userLabel);
        $safeTx = str_replace(["\n", "\r"], ' ', $transactionId);

        $text = "🔔 طلب إيداع جديد\n";
        $text .= 'المعرف: '.$depositId."\n";
        $text .= 'المبلغ: '.$amountUsd.' USD / '.$amountSyp." SYP\n";
        $text .= 'العملة: '.$currency."\n";
        $text .= 'الطريقة: '.$method."\n";
        $text .= 'المستخدم: '.$safeUser."\n";
        $text .= 'رقم العملية: '.$safeTx."\n";
        $text .= 'صورة الإثبات: '.$proofUrl."\n";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'قبول الإيداع', 'callback_data' => 'd:approve:'.$depositId],
                    ['text' => 'رفض الإيداع', 'callback_data' => 'd:reject:'.$depositId],
                ],
            ],
        ];

        return $this->sendMessage($token, (string) $chatId, $text, $keyboard);
    }

    public function answerCallbackQuery(string $botToken, string $callbackQueryId, ?string $text = null): bool
    {
        try {
            $this->http->post('/bot'.$botToken.'/answerCallbackQuery', [
                'json' => array_filter([
                    'callback_query_id' => $callbackQueryId,
                    'text' => $text,
                    'show_alert' => $text !== null,
                ]),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('telegram.answerCallbackQuery: '.$e->getMessage());

            return false;
        }
    }

    public function sendStoreUserMessage(int $telegramUserId, string $text): bool
    {
        $token = config('services.telegram.store_bot_token');
        if (! is_string($token) || $token === '') {
            return false;
        }

        return $this->sendMessage($token, (string) $telegramUserId, $text, null);
    }

    private function sendMessage(string $token, string $chatId, string $text, ?array $replyMarkup): bool
    {
        try {
            $payload = [
                'chat_id' => $chatId,
                'text' => $text,
                'disable_web_page_preview' => true,
            ];
            if ($replyMarkup !== null) {
                $payload['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
            }
            $this->http->post('/bot'.$token.'/sendMessage', [
                'json' => $payload,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('telegram.sendMessage: '.$e->getMessage());

            return false;
        }
    }
}
