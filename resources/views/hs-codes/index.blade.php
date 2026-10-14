<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">HS Codes</h2>
            <a href="{{ route('hs-codes.create') }}">
                <x-primary-button type="button">Add HS Code</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <form method="GET" class="mb-4 flex flex-wrap gap-2">
                <x-text-input name="q" type="text" placeholder="Code or description..."
                    :value="request('q')" class="w-64" />
                <select name="category" class="border-gray-300 rounded-md shadow-sm">
                    <option value="">All categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <x-secondary-button type="submit">Filter</x-secondary-button>
            </form>

            <div class="bg-white shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($hsCodes as $hs)
                            <tr>
                                <td class="px-4 py-3 font-mono">{{ $hs->code }}</td>
                                <td class="px-4 py-3">{{ $hs->description }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $hs->category->name }}</td>
                                <td class="px-4 py-3">{{ $hs->is_active ? 'Active' : 'Inactive' }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('hs-codes.edit', $hs) }}"
                                       class="text-indigo-600 hover:underline mr-3">Edit</a>
                                    <form method="POST" action="{{ route('hs-codes.destroy', $hs) }}"
                                          class="inline" onsubmit="return confirm('Deactivate this HS code?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline">Deactivate</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No HS codes found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $hsCodes->links() }}</div>
        </div>
    </div>
</x-app-layout>
