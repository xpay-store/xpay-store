<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\TelegramWebAppValidator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TelegramWebAppAuth
{
    public function __construct(
        private TelegramWebAppValidator $validator
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $initData = $request->header('X-Telegram-Init-Data');

        if (! is_string($initData) || $initData === '') {
            return response()->json(['message' => 'Unauthorized: missing Telegram init data.'], 401);
        }

        $botToken = config('services.telegram.store_bot_token');
        if (! is_string($botToken) || $botToken === '') {
            return response()->json(['message' => 'Server misconfiguration: store bot token.'], 500);
        }

        $parsed = $this->validator->validateAndParse($initData, $botToken);
        if ($parsed === null) {
            return response()->json(['message' => 'Unauthorized: invalid Telegram signature.'], 401);
        }

        $telegramUser = $parsed['user'] ?? null;
        if (! is_array($telegramUser) || ! isset($telegramUser['id'])) {
            return response()->json(['message' => 'Unauthorized: missing Telegram user.'], 401);
        }

        $telegramId = (int) $telegramUser['id'];
        $username = isset($telegramUser['username']) ? (string) $telegramUser['username'] : null;

        $user = User::query()->where('telegram_id', $telegramId)->first();
        if ($user === null) {
            $user = User::query()->create([
                'telegram_id' => $telegramId,
                'username' => $username,
                'email' => null,
                'balance' => ['USD' => 0, 'SYP' => 0],
                'role' => 'user',
                'is_banned' => false,
                'supabase_uid' => null,
            ]);
        } else {
            if ($username !== null && $user->username !== $username) {
                $user->username = $username;
                $user->save();
            }
        }

        if ($user->is_banned) {
            return response()->json(['message' => 'Account is banned.'], 403);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
