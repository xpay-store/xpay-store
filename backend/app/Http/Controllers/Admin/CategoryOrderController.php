<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryOrderController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*.id' => ['required', 'string'],
            'order.*.order' => ['required', 'integer'],
        ]);

        foreach ($data['order'] as $row) {
            $cat = Category::query()->where('_id', $row['id'])->first();
            if ($cat !== null) {
                $cat->order = (int) $row['order'];
                $cat->save();
            }
        }

        return response()->json(['message' => 'Updated.']);
    }
}
