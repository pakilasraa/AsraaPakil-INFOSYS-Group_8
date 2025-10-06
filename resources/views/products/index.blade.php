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
                        <span class="text-cafe-900 font-medium whitespace-nowrap">₱{{ number_format($product->price, 2) }}</span>
                    </div>
                    <div>
                        <span class="inline-block text-xs px-2 py-1 rounded-full bg-amber-100 text-cafe-900">{{ $product->category?->name }}</span>
                    </div>
                    <div class="flex gap-2 pt-2 mt-auto">
                        <button @click="$dispatch('open-edit', {id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', price: '{{ $product->price }}', category_id: {{ $product->category_id }} })" class="px-3 py-1.5 rounded-md bg-amber-200 text-cafe-900">Edit</button>
                        <form method="POST" action="{{ route('products.destroy', $product) }}">
                            @csrf
                            @method('DELETE')
                            <button class="px-3 py-1.5 rounded-md bg-red-100 text-red-700" onclick="return confirm('Delete this product?')">Delete</button>
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
                        <label class="text-sm text-cafe-900">Price</label>
                        <input type="number" name="price" step="0.01" min="0" required class="w-full rounded-lg border input-cafe px-3 py-2" />
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
    <div x-data="{open:false, id:null, name:'', price:'', category_id:''}" x-cloak
         x-on:open-edit.window="open=true; id=$event.detail.id; name=$event.detail.name; price=$event.detail.price; category_id=$event.detail.category_id">
        <div x-cloak x-show="open" class="fixed inset-0 bg-black/40 z-40" @click="open=false"></div>
        <div x-cloak x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow w-full max-w-md">
                <div class="p-4 border-b flex items-center"><h2 class="font-semibold">Edit Product</h2><button class="ml-auto" @click="open=false">✖</button></div>
                <form method="POST" :action="`/products/${id}`" enctype="multipart/form-data" class="p-4 space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="text-sm text-cafe-900">Name</label>
                        <input name="name" x-model="name" required class="w-full rounded-lg border input-cafe px-3 py-2" />
                    </div>
                    <div>
                        <label class="text-sm text-cafe-900">Price</label>
                        <input type="number" name="price" x-model="price" step="0.01" min="0" required class="w-full rounded-lg border input-cafe px-3 py-2" />
                    </div>
                    <div>
                        <label class="text-sm text-cafe-900">Category</label>
                        <select name="category_id" x-model="category_id" required class="w-full rounded-lg border input-cafe px-3 py-2">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-cafe-900">Image (optional)</label>
                        <input type="file" name="image" accept="image/*" class="w-full rounded-lg border input-cafe px-3 py-2" />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="open=false" class="px-3 py-2 rounded-md bg-gray-100">Cancel</button>
                        <button class="px-4 py-2 rounded-md btn-cafe">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>


