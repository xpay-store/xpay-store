<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categoryId = $request->query('category_id');
        $q = Product::query()->where('available', true);

        if (is_string($categoryId) && $categoryId !== '') {
            $q->where('category_id', $categoryId);
        }

        $products = $q->orderBy('name')->limit(500)->get();

        return response()->json(['data' => $products]);
    }

    public function search(Request $request): JsonResponse
    {
        $term = (string) $request->query('q', '');
        if ($term === '') {
            return response()->json(['data' => []]);
        }

        $products = Product::query()
            ->where('available', true)
            ->where('name', 'like', '%'.$term.'%')
            ->orderBy('name')
            ->limit(100)
            ->get();

        return response()->json(['data' => $products]);
    }
}
