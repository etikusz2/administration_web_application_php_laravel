<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Crearea unui nou departament
            </h2>
            <a href="{{ url('/services') }}"
                class="text-sm text-gray-600 hover:text-gray-900 bg-gray-200 hover:bg-gray-300 rounded-md px-4 py-2">
                Înapoi
            </a>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('services.storeDepartment') }}">
                        @csrf

                        <!-- Deparment Name -->
                        <div class="mb-4">
                            <label for="departmentName"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Denumirea
                                departamentului</label>
                            <input type="text" name="departmentName" id="departmentName"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 bg-gray-50 dark:bg-gray-700 dark:text-gray-300"
                                required>
                        </div>

                        <!-- Webapplication -->
                        <div class="mb-4">
                            <label for="webApplicationId"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aplicația web
                                (opțional)</label>
                            <select name="webApplicationId" id="webApplicationId"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                                <option value="">Aplicații web...</option>
                                @foreach ($webApplications as $webApplication)
                                    <option value="{{ $webApplication->id }}">{{ $webApplication->webApplicationName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Webapplication URL -->
                        <div class="mb-4">
                            <label for="webApplicationURL"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adresa aplicația web
                                (URL) (opțional)</label>
                            <input type="text" name="webApplicationURL" id="webApplicationURL"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        </div>

                        <!-- Submit button -->
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Adaugă
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
