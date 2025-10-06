<x-app-layout>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold text-cafe-900">Categories</h1>
        <button @click="$dispatch('open-modal', 'create-category')" class="px-4 py-2 rounded-lg btn-cafe">Add Category</button>
    </div>

    <!-- Table / Empty State -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if ($categories->count() === 0)
            <div class="p-10 text-center space-y-2">
                <div class="text-3xl">📂</div>
                <h3 class="text-cafe-900 font-semibold">No categories yet</h3>
                <p class="text-cafe-700 text-sm">Create your first category to organize products.</p>
                <div class="pt-2">
                    <button @click="$dispatch('open-modal', 'create-category')" class="px-4 py-2 rounded-lg btn-cafe">Add Category</button>
                </div>
            </div>
        @else
        <div class="overflow-x-auto hidden md:block">
            <table class="min-w-full">
                <thead class="bg-[#5b4334] text-white">
                    <tr>
                        <th class="text-left px-4 py-3">ID</th>
                        <th class="text-left px-4 py-3">Name</th>
                        <th class="text-left px-4 py-3">Description</th>
                        <th class="text-right px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr class="odd:bg-[#faf5ef] even:bg-white border-b">
                            <td class="px-4 py-3">{{ $category->id }}</td>
                            <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                            <td class="px-4 py-3 text-sm text-cafe-700">{{ $category->description }}</td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <button @click="$dispatch('open-edit', {id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', description: '{{ addslashes($category->description ?? '') }}'})" class="px-3 py-1.5 rounded-md bg-amber-200 text-cafe-900">Edit</button>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-md bg-red-100 text-red-700" onclick="return confirm('Delete this category?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile cards -->
        <div class="md:hidden divide-y">
            @foreach($categories as $category)
                <div class="p-4 bg-[#faf5ef]">
                    <div class="flex justify-between">
                        <div>
                            <div class="text-xs text-cafe-700">ID: {{ $category->id }}</div>
                            <div class="font-medium">{{ $category->name }}</div>
                            <div class="text-sm text-cafe-700">{{ $category->description }}</div>
                        </div>
                        <div class="space-x-2">
                            <button @click="$dispatch('open-edit', {id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', description: '{{ addslashes($category->description ?? '') }}'})" class="px-3 py-1.5 rounded-md bg-amber-200 text-cafe-900">Edit</button>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="px-3 py-1.5 rounded-md bg-red-100 text-red-700">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>

    <!-- Create Modal -->
    <div x-data="{open:false}" x-on:open-modal.window="if($event.detail==='create-category') open=true" x-cloak>
        <div x-cloak x-show="open" class="fixed inset-0 bg-black/40 z-40" @click="open=false"></div>
        <div x-cloak x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow w-full max-w-md">
                <div class="p-4 border-b flex items-center"><h2 class="font-semibold">Add Category</h2><button class="ml-auto" @click="open=false">✖</button></div>
                <form method="POST" action="{{ route('categories.store') }}" class="p-4 space-y-3">
                    @csrf
                    <div>
                        <label class="text-sm text-cafe-900">Name</label>
                        <input name="name" required class="w-full rounded-lg border input-cafe px-3 py-2" />
                        @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm text-cafe-900">Description</label>
                        <textarea name="description" rows="3" class="w-full rounded-lg border input-cafe px-3 py-2"></textarea>
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
    <div x-data="{open:false, id:null, name:'', description:''}" x-cloak
         x-on:open-edit.window="open=true; id=$event.detail.id; name=$event.detail.name; description=$event.detail.description">
        <div x-cloak x-show="open" class="fixed inset-0 bg-black/40 z-40" @click="open=false"></div>
        <div x-cloak x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow w-full max-w-md">
                <div class="p-4 border-b flex items-center"><h2 class="font-semibold">Edit Category</h2><button class="ml-auto" @click="open=false">✖</button></div>
                <form method="POST" :action="`/categories/${id}`" class="p-4 space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="text-sm text-cafe-900">Name</label>
                        <input name="name" x-model="name" required class="w-full rounded-lg border input-cafe px-3 py-2" />
                    </div>
                    <div>
                        <label class="text-sm text-cafe-900">Description</label>
                        <textarea name="description" rows="3" x-model="description" class="w-full rounded-lg border input-cafe px-3 py-2"></textarea>
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


