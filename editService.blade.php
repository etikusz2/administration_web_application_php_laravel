<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Editare - Serviciul {{ $service->serviceName }}
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
                    <form method="POST"
                        action="{{ route('services.updateService', ['service' => $service->id, 'department' => $department->id]) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nume Serviciu -->
                        <div class="mb-4">
                            <label for="serviceName"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Numele
                                serviciului</label>
                            <input type="text" name="serviceName" id="serviceName"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 bg-gray-50 dark:bg-gray-700 dark:text-gray-300"
                                value="{{ $service->serviceName }}" required>
                        </div>

                        <!-- Descriere Serviciu -->
                        <div class="mb-4">
                            <label for="serviceDescription"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrierea
                                serviciului</label>
                            <textarea name="serviceDescription" id="serviceDescription" rows="3"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 bg-gray-50 dark:bg-gray-700 dark:text-gray-300">{{ $service->serviceDescription }}</textarea>
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
