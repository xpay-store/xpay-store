<?php

namespace Bots;

use App\Models\Deposit;
use App\Models\User;
use App\Services\DepositReviewService;
use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Telegram @XPayDEPOSITBot webhook handler (mounted in routes/api.php).
 */
final class DepositBotWebhook
{
    public function __construct(
        private DepositReviewService $reviews,
        private TelegramBotService $telegram
    ) {}

    public function __invoke(Request $request, string $secret): JsonResponse|Response
    {
        $expected = (string) config('services.telegram.deposit_webhook_secret');
        if ($expected === '' || ! hash_equals($expected, $secret)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $update = $request->all();
        $cb = $update['callback_query'] ?? null;
        if (! is_array($cb)) {
            return response()->json(['ok' => true]);
        }

        $from = $cb['from'] ?? null;
        $telegramUserId = is_array($from) ? (int) ($from['id'] ?? 0) : 0;
        $callbackQueryId = is_string($cb['id'] ?? null) ? $cb['id'] : null;
        $data = is_string($cb['data'] ?? null) ? $cb['data'] : '';

        $token = (string) config('services.telegram.deposit_bot_token');

        if ($telegramUserId === 0 || $data === '') {
            if ($callbackQueryId !== null && $token !== '') {
                $this->telegram->answerCallbackQuery($token, $callbackQueryId, null);
            }

            return response()->json(['ok' => true]);
        }

        if (! $this->isAllowedStaff($telegramUserId)) {
            if ($callbackQueryId !== null && $token !== '') {
                $this->telegram->answerCallbackQuery($token, $callbackQueryId, 'غير مصرح لك بتنفيذ هذا الإجراء.');
            }

            return response()->json(['ok' => true]);
        }

        $parts = explode(':', $data, 3);
        if (count($parts) !== 3 || $parts[0] !== 'd' || ! in_array($parts[1], ['approve', 'reject'], true)) {
            if ($callbackQueryId !== null && $token !== '') {
                $this->telegram->answerCallbackQuery($token, $callbackQueryId, null);
            }

            return response()->json(['ok' => true]);
        }

        $action = $parts[1];
        $depositId = $parts[2];

        $deposit = Deposit::query()->where('_id', $depositId)->first();
        if ($deposit === null) {
            if ($callbackQueryId !== null && $token !== '') {
                $this->telegram->answerCallbackQuery($token, $callbackQueryId, 'العملية غير موجودة.');
            }

            return response()->json(['ok' => true]);
        }

        $label = 'tg:'.$telegramUserId;
        if ($action === 'approve') {
            $this->reviews->approve($deposit, $label);
            if ($callbackQueryId !== null && $token !== '') {
                $this->telegram->answerCallbackQuery($token, $callbackQueryId, 'تم قبول الإيداع.');
            }
        } else {
            $this->reviews->reject($deposit, $label);
            if ($callbackQueryId !== null && $token !== '') {
                $this->telegram->answerCallbackQuery($token, $callbackQueryId, 'تم رفض الإيداع.');
            }
        }

        return response()->json(['ok' => true]);
    }

    private function isAllowedStaff(int $telegramUserId): bool
    {
        $allowed = config('services.telegram.allowed_callback_user_ids');
        if (is_array($allowed) && in_array($telegramUserId, $allowed, true)) {
            return true;
        }

        $user = User::query()->where('telegram_id', $telegramUserId)->first();
        if ($user === null) {
            return false;
        }

        return $user->isStaff();
    }
}
