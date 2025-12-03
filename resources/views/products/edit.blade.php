<x-app-layout>
    <div class="max-w-3xl mx-auto mt-8 bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-semibold mb-4">Edit Product</h1>

        @if (session('status'))
            <div class="mb-4 text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 text-sm text-red-600">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('products.update', $product->id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-4"
        >
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $product->name) }}"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2"
                    required
                >
            </div>

            {{-- Category (optional kung may $categories ka) --}}
            @isset($categories)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select
                        name="category_id"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2"
                        required
                    >
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected(old('category_id', $product->category_id) == $category->id)
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endisset

            {{-- Base + size prices --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Base Price (optional)</label>
                <input
                    type="number"
                    name="price"
                    step="0.01"
                    min="0"
                    value="{{ old('price', $product->price) }}"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2"
                    placeholder="If empty, will use one of the size prices"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Small Price</label>
                    <input
                        type="number"
                        name="price_small"
                        step="0.01"
                        min="0"
                        value="{{ old('price_small', $product->price_small) }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Medium Price</label>
                    <input
                        type="number"
                        name="price_medium"
                        step="0.01"
                        min="0"
                        value="{{ old('price_medium', $product->price_medium) }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Large Price</label>
                    <input
                        type="number"
                        name="price_large"
                        step="0.01"
                        min="0"
                        value="{{ old('price_large', $product->price_large) }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2"
                    >
                </div>
            </div>

            {{-- Image --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Image</label>

                    @if($product->image)
                        <div class="mb-2 flex items-center gap-3">
                            <img
                                src="{{ asset('storage/' . $product->image) }}"
                                alt="{{ $product->name }}"
                                class="h-20 rounded"
                            >
                            <label class="inline-flex items-center text-sm text-gray-700">
                                <input
                                    type="checkbox"
                                    name="remove_image"
                                    value="1"
                                    class="mr-2 rounded border-gray-300"
                                >
                                Remove current image
                            </label>
                        </div>
                    @endif

                    <input
                        type="file"
                        name="image"
                        class="mt-1 block w-full text-sm text-gray-700"
                    >
                    <p class="text-xs text-gray-500 mt-1">
                        Choose a new file to replace the image.
                        Leave empty to keep current image.
                    </p>
                </div>


            {{-- BUTTONS --}}
            <div class="flex items-center justify-between mt-6">
                <a href="{{ url('/products') }}" class="text-sm text-gray-600 hover:underline">
                    ← Back to Products
                </a>

                <button
                    type="submit"
                    class="px-4 py-2 rounded-md bg-cyan-700 text-white text-sm font-medium"
                >
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
