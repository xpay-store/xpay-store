<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = (string) $request->query('q', '');
        $query = User::query();
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('username', 'like', '%'.$q.'%')
                    ->orWhere('telegram_id', (int) $q);
            });
        }

        $users = $query->orderByDesc('created_at')->limit(500)->get();

        return response()->json(['data' => $users]);
    }

    public function adjustBalance(Request $request, string $id): JsonResponse
    {
        $user = User::query()->where('_id', $id)->first();
        if ($user === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $data = $request->validate([
            'delta_usd' => ['sometimes', 'numeric'],
            'delta_syp' => ['sometimes', 'numeric'],
        ]);

        $bal = $user->balance ?? ['USD' => 0, 'SYP' => 0];
        if (isset($data['delta_usd'])) {
            $bal['USD'] = (float) ($bal['USD'] ?? 0) + (float) $data['delta_usd'];
        }
        if (isset($data['delta_syp'])) {
            $bal['SYP'] = (float) ($bal['SYP'] ?? 0) + (float) $data['delta_syp'];
        }
        $user->balance = $bal;
        $user->save();

        return response()->json(['user' => $user]);
    }
}
