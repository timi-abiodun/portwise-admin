<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Categories</h2>
            <a href="{{ route('categories.create') }}">
                <x-primary-button type="button">Add Category</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <form method="GET" class="mb-4 flex gap-2">
                <x-text-input name="q" type="text" placeholder="Search by name..."
                    :value="request('q')" class="w-64" />
                <x-secondary-button type="submit">Search</x-secondary-button>
            </form>

            <div class="bg-white shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($categories as $category)
                            <tr>
                                <td class="px-4 py-3">{{ $category->name }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $category->slug }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('categories.edit', $category) }}"
                                       class="text-indigo-600 hover:underline mr-3">Edit</a>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                          class="inline" onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">No categories found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $categories->links() }}</div>
        </div>
    </div>
</x-app-layout>
