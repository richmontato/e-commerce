<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white leading-tight">Add New Product</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block font-medium mb-1">Product Name *</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            @class([
                                'w-full rounded-md focus:outline-none',
                                'border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200' => !$errors->has('name'),
                                'border border-red-500 focus:border-red-500 focus:ring focus:ring-red-200' => $errors->has('name'),
                            ])
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-medium mb-1">Description</label>
                        <textarea name="description" rows="4" class="border-gray-300 rounded-md w-full">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium mb-1">Price (Rp) *</label>
                            <input type="number" name="price" value="{{ old('price') }}" required min="0"
                                   class="border-gray-300 rounded-md w-full">
                        </div>
                        <div>
                            <label class="block font-medium mb-1">Stock *</label>
                            <input type="number" name="stock" value="{{ old('stock', 0) }}" required min="0"
                                   class="border-gray-300 rounded-md w-full">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium mb-1">Category</label>
                        <input type="text" name="category" value="{{ old('category') }}" 
                               class="border-gray-300 rounded-md w-full">
                    </div>

                    <div>
                        <label class="block font-medium mb-1">Image URL</label>
                        <input type="url" name="image_url" value="{{ old('image_url') }}" 
                               placeholder="https://example.com/image.jpg"
                               class="border-gray-300 rounded-md w-full">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked 
                               class="rounded border-gray-300 mr-2">
                        <label>Active (visible to customers)</label>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('products.manage') }}" class="px-4 py-2 border rounded-md hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md">
                            Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
