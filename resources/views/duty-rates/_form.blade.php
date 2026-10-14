@csrf

<div x-data="{ type: '{{ old('rate_type', $rate?->rate_type ?? 'ad_valorem') }}' }">

    <div>
        <x-input-label for="hs_code_id" value="HS code" />
        <select id="hs_code_id" name="hs_code_id" required
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">Select an HS code</option>
            @foreach ($hsCodes as $hs)
                <option value="{{ $hs->id }}" @selected(old('hs_code_id', $rate?->hs_code_id) == $hs->id)>
                    {{ $hs->code }}: {{ $hs->description }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('hs_code_id')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="rate_type" value="Rate type" />
        <select id="rate_type" name="rate_type" x-model="type"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="ad_valorem">Percentage of value (ad valorem)</option>
            <option value="per_kg">Per kilogram</option>
        </select>
        <x-input-error :messages="$errors->get('rate_type')" class="mt-2" />
    </div>

    <div class="mt-4" x-show="type === 'ad_valorem'">
        <x-input-label for="duty_pct" value="Duty (%)" />
        <x-text-input id="duty_pct" name="duty_pct" type="number" step="0.001" min="0" max="100"
            class="mt-1 block w-full" :value="old('duty_pct', $rate?->duty_pct)" />
        <x-input-error :messages="$errors->get('duty_pct')" class="mt-2" />
    </div>

    <div class="mt-4" x-show="type === 'per_kg'">
        <x-input-label for="rate_per_kg" value="Rate (NGN per kg)" />
        <x-text-input id="rate_per_kg" name="rate_per_kg" type="number" step="0.01" min="0"
            class="mt-1 block w-full" :value="old('rate_per_kg', $rate?->rate_per_kg)" />
        <x-input-error :messages="$errors->get('rate_per_kg')" class="mt-2" />
    </div>

    <div class="mt-4 grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="effective_from" value="Effective from" />
            <x-text-input id="effective_from" name="effective_from" type="date" class="mt-1 block w-full"
                :value="old('effective_from', $rate?->effective_from?->toDateString() ?? now()->toDateString())" required />
            <x-input-error :messages="$errors->get('effective_from')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="effective_to" value="Effective to (leave empty if current)" />
            <x-text-input id="effective_to" name="effective_to" type="date" class="mt-1 block w-full"
                :value="old('effective_to', $rate?->effective_to?->toDateString())" />
            <x-input-error :messages="$errors->get('effective_to')" class="mt-2" />
        </div>
    </div>

    <div class="mt-4">
        <x-input-label for="source_ref" value="Source reference (e.g. NCS Tariff, p.114)" />
        <x-text-input id="source_ref" name="source_ref" type="text" class="mt-1 block w-full"
            :value="old('source_ref', $rate?->source_ref)" />
        <x-input-error :messages="$errors->get('source_ref')" class="mt-2" />
    </div>

    <div class="mt-6 flex items-center gap-3">
        <x-primary-button>Save</x-primary-button>
        <a href="{{ route('duty-rates.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
    </div>
</div>
