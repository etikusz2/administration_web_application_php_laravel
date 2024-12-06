<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Editare - {{ $serviceWindow->windowNamename }} Ghișeu
            </h2>
            <a href="{{ url('/services') }}"
                class="text-sm text-gray-600 hover:text-gray-900 bg-gray-200 hover:bg-gray-300 rounded-md px-4 py-2">
                Înapoi
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('services.updateServiceWindow', $serviceWindow->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Select services -->
                        <div x-data="{ open: false, selectedServices: [] }" class="relative mt-4">
                            <x-input-label for="services" :value="__('Selectați serviciile disponibile')" />

                            <!-- Button to toggle dropdown -->
                            <button type="button" @click="open = !open"
                                class="block w-full bg-white border border-gray-300 rounded-md shadow-sm p-2 text-left">
                                Alegeți serviciile disponibile </button>

                            <!-- Dropdown List -->
                            <div x-show="open" @click.away="open = false"
                                class="absolute w-full max-h-60 overflow-y-auto bg-white border border-gray-300 mt-1 rounded-md shadow-lg z-10">
                                @foreach ($services as $service)
                                    <label class="block">
                                        <input type="checkbox" name="services[]" value="{{ $service->id }}"
                                            x-model="selectedServices" @if (in_array($service->id, $serviceWindow->services->pluck('id')->toArray())) checked @endif>
                                        {{ $service->serviceName }}
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('services')" class="mt-2" />
                        </div>

                        <!-- Nume Ghișeu -->
                        <div class="mb-4">
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Numele
                                ghișeului</label>
                            <input type="text" name="name" id="name"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 bg-gray-50 dark:bg-gray-700 dark:text-gray-300"
                                value="{{ $serviceWindow->windowName }}" required>
                        </div>

                        <!-- Adresa Ghișeu -->
                        <div class="mb-4">
                            <label for="location"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adresa
                                ghișeului</label>
                            <textarea name="location" id="location" rows="3"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50 dark:bg-gray-700 dark:text-gray-300">{{ $serviceWindow->windowLocation }}</textarea>
                        </div>

                        <!-- Butonul de trimitere -->
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Salvare
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
