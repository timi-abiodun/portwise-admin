<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Duty Rates</h2>
            <a href="{{ route('duty-rates.create') }}">
                <x-primary-button type="button">Add Duty Rate</x-primary-button>
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
                <select name="status" class="border-gray-300 rounded-md shadow-sm">
                    <option value="">All rates</option>
                    <option value="current" @selected(request('status') === 'current')>Current only</option>
                    <option value="ended" @selected(request('status') === 'ended')>Ended only</option>
                </select>
                <x-secondary-button type="submit">Filter</x-secondary-button>
            </form>

            <div class="bg-white shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">HS code</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rate</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">From</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">To</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($rates as $rate)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="font-mono">{{ $rate->hsCode->code }}</span>
                                    <div class="text-xs text-gray-500">{{ $rate->hsCode->description }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $rate->hsCode->category->name }}</td>
                                <td class="px-4 py-3">
                                    @if ($rate->rate_type === 'ad_valorem')
                                        {{ rtrim(rtrim($rate->duty_pct, '0'), '.') }}%
                                    @else
                                        ₦{{ number_format($rate->rate_per_kg, 2) }}/kg
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $rate->effective_from->toDateString() }}</td>
                                <td class="px-4 py-3">{{ $rate->effective_to?->toDateString() ?? 'Current' }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $rate->source_ref }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('duty-rates.edit', $rate) }}"
                                       class="text-indigo-600 hover:underline mr-3">Edit</a>
                                    <form method="POST" action="{{ route('duty-rates.destroy', $rate) }}"
                                          class="inline" onsubmit="return confirm('Delete this duty rate?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">No duty rates found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $rates->links() }}</div>
        </div>
    </div>
</x-app-layout>
