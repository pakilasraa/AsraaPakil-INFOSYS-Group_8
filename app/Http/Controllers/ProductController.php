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
        $products = Product::with('category')
            ->when($request->input('category'), function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get(['id','name']);

        return view('products.index', compact('products','categories'));
    }

    public function store(Request $request): RedirectResponse
    {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'price' => ['nullable', 'numeric', 'min:0'],
        'price_small' => ['nullable', 'numeric', 'min:0'],
        'price_medium' => ['nullable', 'numeric', 'min:0'],
        'price_large' => ['nullable', 'numeric', 'min:0'],
        'category_id' => ['required', 'exists:categories,id'],
        'image' => ['nullable', 'image', 'max:2048'],
    ]);

    // --- IMAGE UPLOAD (same logic as dati) ---
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

    // --- SIZE PRICING LOGIC ---

    // 1) Kailangan may kahit isang price
    $hasAnyPrice =
        (!empty($validated['price'])) ||
        (!empty($validated['price_small'])) ||
        (!empty($validated['price_medium'])) ||
        (!empty($validated['price_large']));

    if (! $hasAnyPrice) {
        return back()
            ->withErrors(['price' => 'Please provide at least one price (base or size-based).'])
            ->withInput();
    }

    // 2) Kung walang base price, gawin nating base ang Medium (o unang available)
    if (empty($validated['price'])) {
        if (!empty($validated['price_medium'])) {
            $validated['price'] = $validated['price_medium']; // default: Medium
        } elseif (!empty($validated['price_small'])) {
            $validated['price'] = $validated['price_small'];
        } elseif (!empty($validated['price_large'])) {
            $validated['price'] = $validated['price_large'];
        }
    }

    Product::create($validated);

    return back()->with('status', 'Product created');
}


    public function update(Request $request, Product $product): \Illuminate\Http\RedirectResponse
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'price' => ['nullable', 'numeric', 'min:0'],
        'price_small' => ['nullable', 'numeric', 'min:0'],
        'price_medium' => ['nullable', 'numeric', 'min:0'],
        'price_large' => ['nullable', 'numeric', 'min:0'],
        'category_id' => ['required', 'exists:categories,id'],
        'image' => ['nullable', 'image', 'max:2048'],
        'remove_image' => ['nullable', 'boolean'],
    ]);

    // --- IMAGE HANDLING ---

    // 1) Kung may bagong image na in-upload
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        if ($file && $file->isValid()) {

            // burahin ang lumang image file kung meron
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
    } else {
        // 2) Kung walang bagong image BUT naka-check ang "remove_image"
        if ($request->boolean('remove_image')) {
            if ($product->image) {
                @unlink(storage_path('app/public/' . $product->image));
            }
            $validated['image'] = null;
        } else {
            // 3) Walang bagong image at hindi nag-remove → keep current path
            unset($validated['image']); // huwag galawin ang column sa DB
        }
    }

    // --- SIZE PRICING LOGIC ---

    $hasAnyPrice =
        (!empty($validated['price'])) ||
        (!empty($validated['price_small'])) ||
        (!empty($validated['price_medium'])) ||
        (!empty($validated['price_large']));

    if (! $hasAnyPrice) {
        return back()
            ->withErrors(['price' => 'Please provide at least one price (base or size-based).'])
            ->withInput();
    }

    if (empty($validated['price'])) {
        if (!empty($validated['price_medium'])) {
            $validated['price'] = $validated['price_medium'];
        } elseif (!empty($validated['price_small'])) {
            $validated['price'] = $validated['price_small'];
        } elseif (!empty($validated['price_large'])) {
            $validated['price'] = $validated['price_large'];
        }
    }

    $product->update($validated);

    return redirect()
        ->route('products.index')
        ->with('status', 'Product updated');
}


    public function edit(Product $product)
{
    // Kunin lahat ng categories para sa dropdown
    $categories = Category::all(); // kung wala kang Category model, pwede mo muna tanggalin ang line na ito

    return view('products.edit', [
        'product' => $product,
        'categories' => $categories,
    ]);
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


