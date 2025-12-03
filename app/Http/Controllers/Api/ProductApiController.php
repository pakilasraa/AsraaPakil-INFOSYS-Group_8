<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function index()
    {
        return Product::with('category')->paginate(20);
    }

    public function show(Product $product)
    {
        $product->load('category');

        return $product;
    }

    public function categories()
    {
        return Category::all();
    }

    public function byCategory(Category $category)
    {
        return $category->products()->paginate(20);
    }
}
