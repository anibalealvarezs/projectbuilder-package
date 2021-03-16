<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users\' Index') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="grid grid-flow-row auto-rows-max grid-flow-col auto-cols-max py-15 px-20">
                    <div class="col-span-6 sm:col-span-4">
                        <div>User's Index</div>
                        {{-- <x-rate-form rate="{{$rate}}"></x-rate-form> --}}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>