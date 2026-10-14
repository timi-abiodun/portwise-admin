<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Import tariff data (CSV)</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('import_result'))
                @php $result = session('import_result'); @endphp

                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h3 class="font-semibold text-gray-800">Import result</h3>
                    <p class="mt-2 text-green-700">
                        {{ $result['applied'] }} row(s) applied:
                        {{ $result['summary']['codes_created'] }} HS codes created,
                        {{ $result['summary']['codes_updated'] }} updated;
                        {{ $result['summary']['rates_created'] }} rates created,
                        {{ $result['summary']['rates_updated'] }} updated.
                    </p>

                    @if (count($result['errors']))
                        <p class="mt-4 text-red-700 font-medium">{{ count($result['errors']) }} row(s) skipped:</p>
                        <table class="mt-2 min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left">Line</th>
                                    <th class="px-3 py-2 text-left">HS code</th>
                                    <th class="px-3 py-2 text-left">Problem</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($result['errors'] as $err)
                                    <tr>
                                        <td class="px-3 py-2">{{ $err['line'] }}</td>
                                        <td class="px-3 py-2 font-mono">{{ $err['code'] }}</td>
                                        <td class="px-3 py-2">{{ implode(' ', $err['messages']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endif

            <div class="bg-white p-6 shadow sm:rounded-lg">
                <p class="text-sm text-gray-600">
                    One row per HS code. Each row creates or updates the HS code and its current duty rate.
                    Columns: <span class="font-mono">category_slug, hs_code, description, rate_type, duty_pct, rate_per_kg, source_ref</span>.
                    Use <span class="font-mono">ad_valorem</span> with <span class="font-mono">duty_pct</span>,
                    or <span class="font-mono">per_kg</span> with <span class="font-mono">rate_per_kg</span>.
                    <a href="{{ route('import.template') }}" class="text-indigo-600 hover:underline">Download template</a>
                </p>

                <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" class="mt-4">
                    @csrf
                    <input type="file" name="file" accept=".csv,.txt" required>
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    <div class="mt-4">
                        <x-primary-button>Import</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
