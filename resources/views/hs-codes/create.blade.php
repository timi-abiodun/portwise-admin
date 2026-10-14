<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New HS Code</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 bg-white p-6 shadow sm:rounded-lg">
            <form method="POST" action="{{ route('hs-codes.store') }}">
                @include('hs-codes._form', ['hsCode' => null])
            </form>
        </div>
    </div>
</x-app-layout>
