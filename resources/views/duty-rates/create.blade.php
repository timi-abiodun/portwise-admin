<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Duty Rate</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 bg-white p-6 shadow sm:rounded-lg">
            <form method="POST" action="{{ route('duty-rates.store') }}">
                @include('duty-rates._form', ['rate' => null])
            </form>
        </div>
    </div>
</x-app-layout>
