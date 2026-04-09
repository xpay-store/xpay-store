<?php

namespace App\Http\Controllers\AdminUi;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminProductsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = (string) $request->query('q', '');
        $limit = (int) $request->query('limit', 100);
        if ($limit < 1 || $limit > 500) {
            $limit = 100;
        }

        $query = Product::query();
        if ($q !== '') {
            $query->where('name', 'like', '%'.$q.'%');
        }

        $items = $query->orderBy('name')->limit($limit)->get();

        return response()->json(['data' => $items]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::query()->where('_id', $id)->first();
        if ($product === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:500'],
            'available' => ['sometimes', 'boolean'],
            'price' => ['sometimes', 'array'],
            'price.USD' => ['sometimes', 'numeric'],
            'price.SYP' => ['sometimes', 'numeric'],
            'profit_percent' => ['sometimes', 'numeric'],
            'params' => ['sometimes', 'array'],
            'qty_values' => ['sometimes', 'array'],
            'image' => ['sometimes', 'string', 'max:2000'],
            'product_type' => ['sometimes', 'in:amount,package'],
        ]);

        $product->fill($data);
        $product->save();

        return response()->json(['product' => $product]);
    }

    public function destroy(string $id): JsonResponse
    {
        $product = Product::query()->where('_id', $id)->first();
        if ($product === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Deleted.']);
    }
}

