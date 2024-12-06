<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestionează Serviciile
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Departamente Section -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Departamente</h3>
                        <a href="{{ route('services.createDepartment') }}" class="btn btn-primary btn-fab">
                            <i class="material-icons">+</i>
                        </a>
                    </div>
                    <hr class="border-t-2 border-gray-200 dark:border-gray-700 mb-4">
                    @foreach ($departments as $department)
                        <div
                            class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex-1">
                                <h4 class="text-md font-semibold">{{ $department->departmentName }}</h4>
                            </div>
                            <div class="flex items-center">
                                <a href="{{ route('services.editDepartment', $department->id) }}"
                                    class="btn btn-secondary btn-sm mr-2 text-indigo-600 hover:text-indigo-900">Modifică</a>
                                <form method="POST" action="{{ route('services.destroyDepartment', $department->id) }}"
                                    onsubmit="return confirm('Sigur doriți să ștergeți acest departament?');"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-danger btn-sm text-red-600 hover:text-red-900">Șterge</button>
                                </form>
                            </div>
                        </div>
                        <div class="ml-6">
                            <div class="flex justify-between items-center mt-2">
                                <h5 class="text-md font-semibold">Serviciile departamentului</h5>
                                <a href="{{ route('services.createService', ['department' => $department->id]) }}"
                                    class="btn btn-primary btn-fab">
                                    <i class="material-icons">+</i>
                                </a>
                            </div>
                            @foreach ($department->services as $service)
                                <div
                                    class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex-1">
                                        {{ $service->serviceName }}
                                    </div>
                                    <div class="flex items-center">
                                        <a href="{{ route('services.editService', ['service' => $service->id, 'department' => $department->id]) }}"
                                            class="btn btn-secondary btn-sm mr-2 text-indigo-600 hover:text-indigo-900">Modifică</a>
                                        <form method="POST"
                                            action="{{ route('services.destroyService', ['service' => $service->id, 'department' => $department->id]) }}"
                                            onsubmit="return confirm('Sigur doriți să ștergeți acest serviciu?');"
                                            style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-danger btn-sm text-red-600 hover:text-red-900">Șterge</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    <!-- Ghișee Section -->
                    <div class="flex justify-between items-center mt-8 mb-4">
                        <h3 class="text-lg font-semibold">Ghișee</h3>
                        <a href="{{ route('services.createServiceWindow') }}" class="btn btn-primary btn-fab">
                            <i class="material-icons">+</i>
                        </a>
                    </div>
                    <hr class="border-t-2 border-gray-200 dark:border-gray-700 mb-4">
                    @foreach ($serviceWindows as $serviceWindow)
                        <div
                            class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex-1">
                                <h4 class="text-md font-semibold">{{ $serviceWindow->windowName }}, loc.
                                    {{ $serviceWindow->windowLocation }}</h4>
                            </div>
                            <div class="flex items-center">
                                <a href="{{ route('services.editServiceWindow', $serviceWindow->id) }}"
                                    class="btn btn-secondary btn-sm mr-2 text-indigo-600 hover:text-indigo-900">Modifică</a>
                                <form method="POST"
                                    action="{{ route('services.destroyServiceWindow', $serviceWindow->id) }}"
                                    onsubmit="return confirm('Sigur doriți să ștergeți acest ghișeu?');"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-danger btn-sm text-red-600 hover:text-red-900">Șterge</button>
                                </form>
                            </div>
                        </div>
                        <div class="ml-6">
                            <div class="flex justify-between items-center mt-2">
                                <h5 class="text-md font-semibold">Serviciile ghișeului</h5>
                            </div>
                            @foreach ($serviceWindow->services as $service)
                                <div
                                    class="flex justify-between items-center py-1 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex-1">
                                        {{ $service->serviceName }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
