<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')->latest('id')->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get(['id','name']);

        return view('products.index', compact('products','categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file && $file->isValid()) {
                $destinationPath = storage_path('app/public/menu-images');
                if (! is_dir($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $originalName = $file->getClientOriginalName();
                $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '_', $originalName);
                $file->move($destinationPath, $safeName);
                $validated['image'] = 'menu-images/' . $safeName;
            }
        }

        Product::create($validated);

        return back()->with('status', 'Product created');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file && $file->isValid()) {
                if ($product->image) {
                    @unlink(storage_path('app/public/' . $product->image));
                }
                $destinationPath = storage_path('app/public/menu-images');
                if (! is_dir($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $originalName = $file->getClientOriginalName();
                $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '_', $originalName);
                $file->move($destinationPath, $safeName);
                $validated['image'] = 'menu-images/' . $safeName;
            }
        }

        $product->update($validated);

        return back()->with('status', 'Product updated');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            @unlink(storage_path('app/public/' . $product->image));
        }
        $product->delete();

        return back()->with('status', 'Product deleted');
    }
}


