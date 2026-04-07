<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Setting;
use App\Services\SupabaseStorageService;
use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepositController extends Controller
{
    public function __construct(
        private SupabaseStorageService $storage,
        private TelegramBotService $telegram
    ) {}

    public function create(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'method' => ['required', 'in:sham_cash,binance_pay'],
            'currency' => ['required', 'in:USD,SYP'],
            'amount_usd' => ['required', 'numeric', 'min:0.01'],
            'amount_syp' => ['required', 'numeric', 'min:0'],
            'transaction_id' => ['required', 'string', 'max:200'],
            'proof_image' => ['nullable', 'file', 'image', 'max:8192', 'required_without:proof_url'],
            'proof_url' => ['nullable', 'string', 'max:2000', 'required_without:proof_image'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $general = Setting::general();
        $min = (float) ($general->min_deposit_usd ?? 0);
        if ((float) $data['amount_usd'] < $min) {
            return response()->json(['message' => 'Amount below minimum deposit.'], 422);
        }

        $proofUrl = null;
        if ($request->hasFile('proof_image')) {
            $proofUrl = $this->storage->uploadDepositProof($request->file('proof_image'), (string) $user->_id);
            if ($proofUrl === null) {
                return response()->json(['message' => 'Failed to upload proof image.'], 500);
            }
        } elseif (is_string($data['proof_url'] ?? null) && $data['proof_url'] !== '') {
            $proofUrl = $data['proof_url'];
        } else {
            return response()->json(['message' => 'proof_image or proof_url is required.'], 422);
        }

        $deposit = Deposit::query()->create([
            'user_id' => (string) $user->_id,
            'amount' => [
                'USD' => (float) $data['amount_usd'],
                'SYP' => (float) $data['amount_syp'],
            ],
            'currency' => $data['currency'],
            'method' => $data['method'],
            'transaction_id' => $data['transaction_id'],
            'proof_image' => $proofUrl,
            'status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        $label = $user->username ? '@'.$user->username : (string) $user->telegram_id;
        $this->telegram->sendDepositGroupNotification(
            (string) $deposit->_id,
            (float) $data['amount_usd'],
            (float) $data['amount_syp'],
            (string) $data['currency'],
            (string) $data['method'],
            $label,
            $proofUrl,
            (string) $data['transaction_id']
        );

        return response()->json(['deposit' => $deposit], 201);
    }

    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        $items = Deposit::query()
            ->where('user_id', (string) $user->_id)
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        return response()->json(['data' => $items]);
    }
}
