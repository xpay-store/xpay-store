<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\User;

class DepositReviewService
{
    public function __construct(
        private TelegramBotService $telegram,
        private OrderMessageService $messages
    ) {}

    public function approve(Deposit $deposit, ?string $reviewerLabel = null): bool
    {
        if ($deposit->status !== 'pending') {
            return false;
        }

        $user = User::query()->find($deposit->user_id);
        if ($user === null) {
            return false;
        }

        $amount = $deposit->amount ?? ['USD' => 0, 'SYP' => 0];
        $usd = (float) ($amount['USD'] ?? 0);
        $syp = (float) ($amount['SYP'] ?? 0);

        $bal = $user->balance ?? ['USD' => 0, 'SYP' => 0];
        $bal['USD'] = (float) ($bal['USD'] ?? 0) + $usd;
        $bal['SYP'] = (float) ($bal['SYP'] ?? 0) + $syp;
        $user->balance = $bal;
        $user->save();

        $deposit->status = 'approved';
        $deposit->reviewed_by = $reviewerLabel;
        $deposit->reviewed_at = now();
        $deposit->save();

        $text = $this->messages->render('deposit_approved', [
            'amount_usd' => number_format($usd, 2),
            'amount_syp' => number_format($syp, 0),
        ], '✅ تم قبول إيداعك بنجاح. تم تحديث رصيدك.');
        $this->telegram->sendStoreUserMessage((int) $user->telegram_id, $text);

        return true;
    }

    public function reject(Deposit $deposit, ?string $reviewerLabel = null): bool
    {
        if ($deposit->status !== 'pending') {
            return false;
        }

        $user = User::query()->find($deposit->user_id);
        if ($user === null) {
            return false;
        }

        $deposit->status = 'rejected';
        $deposit->reviewed_by = $reviewerLabel;
        $deposit->reviewed_at = now();
        $deposit->save();

        $text = $this->messages->render('deposit_rejected', [], '❌ تم رفض عملية الإيداع. راجع الدعم إذا لزم الأمر.');
        $this->telegram->sendStoreUserMessage((int) $user->telegram_id, $text);

        return true;
    }
}
