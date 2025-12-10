<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * GET /api/products
     * List all products (optional filter by category_name)
     *
     * Examples:
     *  - /api/products                      -> all products
     *  - /api/products?category_name=Coffee -> only Coffee category
     */
    public function index(Request $request)
    {
        // Start query with category eager-loaded
        $query = Product::with('category')->orderBy('name');

        // Optional filter: ?category_name=Something
        $categoryName = $request->query('category_name');

        if (!empty($categoryName)) {
            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        $products = $query->get([
            'id',
            'name',
            'description',
            'category_id',
            'price',
            'price_small',
            'price_medium',
            'price_large',
            'image',
        ]);

        $data = $products->map(function ($product) {
            return [
                'id'            => $product->id,
                'name'          => $product->name,
                'description'   => $product->description,
                'category_id'   => $product->category_id,
                'category_name' => $product->category->name ?? null,
                'price'         => $product->price,
                'price_small'   => $product->price_small,
                'price_medium'  => $product->price_medium,
                'price_large'   => $product->price_large,
                'image_url'     => $product->image ? asset('storage/' . $product->image) : null,
            ];
        });

        return response()->json($data);
    }

    /**
     * GET /api/products/{id}
     * Product details
     */
    public function show($id)
    {
        $product = Product::with('category')
            ->select([
                'id',
                'name',
                'description',
                'category_id',
                'price',
                'price_small',
                'price_medium',
                'price_large',
                'image',
            ])
            ->findOrFail($id);

        return response()->json([
            'id'            => $product->id,
            'name'          => $product->name,
            'description'   => $product->description,
            'category_id'   => $product->category_id,
            'category_name' => $product->category->name ?? null,
            'price'         => $product->price,
            'price_small'   => $product->price_small,
            'price_medium'  => $product->price_medium,
            'price_large'   => $product->price_large,
            'image_url'     => $product->image ? asset('storage/' . $product->image) : null,
        ]);
    }

    /**
     * GET /api/categories
     */
    public function categories()
    {
        return Category::orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * GET /api/categories/{categoryId}/products
     */
    public function byCategory($categoryId)
    {
        $products = Product::with('category')
            ->where('category_id', $categoryId)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'description',
                'category_id',
                'price',
                'price_small',
                'price_medium',
                'price_large',
                'image',
            ]);

        $data = $products->map(function ($product) {
            return [
                'id'            => $product->id,
                'name'          => $product->name,
                'description'   => $product->description,
                'category_id'   => $product->category_id,
                'category_name' => $product->category->name ?? null,
                'price'         => $product->price,
                'price_small'   => $product->price_small,
                'price_medium'  => $product->price_medium,
                'price_large'   => $product->price_large,
                'image_url'     => $product->image ? asset('storage/' . $product->image) : null,
            ];
        });

        return response()->json($data);
    }
}
