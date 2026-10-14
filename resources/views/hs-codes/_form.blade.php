@csrf

<div>
    <x-input-label for="category_id" value="Category" />
    <select id="category_id" name="category_id" required
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="">Select a category</option>
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" @selected(old('category_id', $hsCode?->category_id) == $cat->id)>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="code" value="HS code (e.g. 8517.13)" />
    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
        :value="old('code', $hsCode?->code)" required />
    <x-input-error :messages="$errors->get('code')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" value="Description" />
    <x-text-input id="description" name="description" type="text" class="mt-1 block w-full"
        :value="old('description', $hsCode?->description)" required />
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<label class="mt-4 flex items-center gap-2">
    <input type="checkbox" name="is_active" value="1"
        @checked(old('is_active', $hsCode?->is_active ?? true))>
    Active
</label>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ route('hs-codes.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
</div>
