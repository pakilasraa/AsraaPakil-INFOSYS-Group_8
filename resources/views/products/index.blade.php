<x-app-layout>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold text-cafe-900">Products</h1>
        <button @click="$dispatch('open-modal', 'create-product')" class="px-4 py-2 rounded-lg btn-cafe">Add Product</button>
    </div>

    <!-- Grid / Empty State -->
    @if ($products->count() === 0)
        <div class="bg-white rounded-lg shadow p-10 text-center space-y-2">
            <div class="text-3xl">🍵</div>
            <h3 class="text-cafe-900 font-semibold">No products yet</h3>
            <p class="text-cafe-700 text-sm">Add your first product to get started.</p>
            <div class="pt-2">
                <button @click="$dispatch('open-modal', 'create-product')" class="px-4 py-2 rounded-lg btn-cafe">Add Product</button>
            </div>
        </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
        @foreach($products as $product)
            <div class="bg-white rounded-xl shadow overflow-hidden flex flex-col h-full">
                <div class="bg-[#faf5ef] h-32 sm:h-40 md:h-44 xl:h-48">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                    @else
                        <div class="w-full h-full flex items-center justify-center text-cafe-700">No Image</div>
                    @endif
                </div>
                    <div class="p-4 flex flex-col gap-2 grow">
                        <div class="flex items-start justify-between gap-2 min-h-[3.25rem]">
                            <h3 class="font-semibold product-name">{{ $product->name }}</h3>

                            @if(!empty($product->description))
                                <p class="text-xs text-cafe-700 line-clamp-2">
                                    {{ $product->description }}
                                </p>
                            @endif

                            <div class="flex flex-col items-end">
                                <span class="text-cafe-900 font-medium whitespace-nowrap">
                                    @php
                                        $displayPrice =
                                            $product->price ??
                                            $product->price_small ??
                                            $product->price_medium ??
                                            $product->price_large ??
                                            0;
                                    @endphp
                                    ₱{{ number_format($displayPrice, 2) }}
                                </span>


                                @if($product->price_small || $product->price_medium || $product->price_large)
                                    <div class="mt-1 text-[11px] text-cafe-700 text-right space-y-0.5">
                                        @if($product->price_small)
                                            <div>S: ₱{{ number_format($product->price_small, 2) }}</div>
                                        @endif
                                        @if($product->price_medium)
                                            <div>M: ₱{{ number_format($product->price_medium, 2) }}</div>
                                        @endif
                                        @if($product->price_large)
                                            <div>L: ₱{{ number_format($product->price_large, 2) }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="mt-3 flex justify-end gap-2">
                            {{-- Edit --}}
                            <a
                                href="{{ route('products.edit', $product->id) }}"
                                class="px-3 py-1.5 rounded-md bg-amber-200 text-cafe-900 text-xs font-medium"
                            >
                                Edit
                            </a>

                            {{-- Delete --}}
                            <form
                                action="{{ route('products.destroy', $product->id) }}"
                                method="POST"
                                onsubmit="return confirm('Delete this product?');"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="px-3 py-1.5 rounded-md bg-red-500 text-white text-xs font-medium"
                                >
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>

            </div>
        @endforeach
    </div>
    @endif

    <div class="mt-4">{{ $products->links() }}</div>

    <!-- Create Modal -->
    <div x-data="{open:false}" x-on:open-modal.window="if($event.detail==='create-product') open=true" x-cloak>
        <div x-cloak x-show="open" class="fixed inset-0 bg-black/40 z-40" @click="open=false"></div>
        <div x-cloak x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow w-full max-w-md">
                <div class="p-4 border-b flex items-center"><h2 class="font-semibold">Add Product</h2><button class="ml-auto" @click="open=false">✖</button></div>
                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="p-4 space-y-3">
                    @csrf
                    <div>
                        <label class="text-sm text-cafe-900">Name</label>
                        <input name="name" required class="w-full rounded-lg border input-cafe px-3 py-2" />
                        @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm text-cafe-900">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full rounded-lg border input-cafe px-3 py-2">{{ old('description') }}</textarea>
                        @error('description')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="text-sm text-cafe-900">Base Price (optional)</label>
                        <input
                            type="number"
                            name="price"
                            step="0.01"
                            min="0"
                            class="w-full rounded-lg border input-cafe px-3 py-2"
                            placeholder="If empty, will use one of the size prices"
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-3">
                        <div>
                            <label class="text-sm text-cafe-900">Small Price</label>
                            <input
                                type="number"
                                name="price_small"
                                step="0.01"
                                min="0"
                                class="w-full rounded-lg border input-cafe px-3 py-2"
                            />
                        </div>
                        <div>
                            <label class="text-sm text-cafe-900">Medium Price</label>
                            <input
                                type="number"
                                name="price_medium"
                                step="0.01"
                                min="0"
                                class="w-full rounded-lg border input-cafe px-3 py-2"
                            />
                        </div>
                        <div>
                            <label class="text-sm text-cafe-900">Large Price</label>
                            <input
                                type="number"
                                name="price_large"
                                step="0.01"
                                min="0"
                                class="w-full rounded-lg border input-cafe px-3 py-2"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="text-sm text-cafe-900">Category</label>
                        <select name="category_id" required class="w-full rounded-lg border input-cafe px-3 py-2">
                            <option value="" disabled selected>Select category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-cafe-900">Image</label>
                        <input type="file" name="image" accept="image/*" class="w-full rounded-lg border input-cafe px-3 py-2" />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="open=false" class="px-3 py-2 rounded-md bg-gray-100">Cancel</button>
                        <button class="px-4 py-2 rounded-md btn-cafe">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div
    x-data="{
        open:false,
        id:null,
        name:'',
        price:'',
        price_small:'',
        price_medium:'',
        price_large:'',
        category_id:''
    }"
    x-cloak
    x-on:open-edit.window="
        open = true;
        id = $event.detail.id;
        name = $event.detail.name;
        price = $event.detail.price ?? '';
        price_small = $event.detail.price_small ?? '';
        price_medium = $event.detail.price_medium ?? '';
        price_large = $event.detail.price_large ?? '';
        category_id = $event.detail.category_id;
    "
>

</x-app-layout>


