<?php

namespace App\Http\Controllers\AdminUi;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUsersController extends Controller
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
}

