<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Unirse a un Equipo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6 flex justify-between items-center">
                <form method="GET" action="{{ route('participante.equipos.join') }}" class="flex items-center gap-4">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por Evento:</label>
                    <select name="evento_id" onchange="this.form.submit()"
                        class="rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 pl-3 pr-10">
                        <option value="">Todos los eventos activos</option>
                        @foreach ($eventos as $evento)
                            <option value="{{ $evento->id }}"
                                {{ request('evento_id') == $evento->id ? 'selected' : '' }}>
                                {{ $evento->nombre }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('error'))
                        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800"
                            role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($equiposDisponibles->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($equiposDisponibles as $equipo)
                                <div
                                    class="border border-gray-200 dark:border-gray-700 rounded-lg p-5 flex flex-col justify-between hover:border-indigo-500 transition duration-150">

                                    <div>
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-bold text-lg truncate w-2/3" title="{{ $equipo->nombre }}">
                                                {{ $equipo->nombre }}
                                            </h4>
                                            <span
                                                class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 whitespace-nowrap">
                                                {{ $equipo->participantes_count }} / 5
                                            </span>
                                        </div>

                                        <div
                                            class="text-xs text-indigo-600 dark:text-indigo-400 font-bold mb-2 uppercase">
                                            {{ $equipo->proyecto->evento->nombre ?? 'Evento Desconocido' }}
                                        </div>

                                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                            Proyecto: <span
                                                class="italic">{{ $equipo->proyecto->nombre ?? 'Sin definir' }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2 line-clamp-2">
                                            {{ $equipo->proyecto->descripcion ?? 'Sin descripción' }}
                                        </p>
                                    </div>

                                    <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-700">
                                        <form method="POST" action="{{ route('participante.equipos.join.store') }}">
                                            @csrf
                                            <input type="hidden" name="equipo_id" value="{{ $equipo->id }}">

                                            <div class="mb-3">
                                                <label for="rol_{{ $equipo->id }}"
                                                    class="text-xs text-gray-500 dark:text-gray-400 block mb-1">
                                                    Selecciona tu rol:
                                                </label>
                                                <select id="rol_{{ $equipo->id }}" name="perfil_id"
                                                    class="w-full text-xs rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5"
                                                    required>
                                                    @foreach ($perfiles as $perfil)
                                                        <option value="{{ $perfil->id }}">{{ $perfil->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <x-primary-button class="w-full justify-center text-xs">
                                                {{ __('Unirse al Equipo') }}
                                            </x-primary-button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay equipos
                                disponibles</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if (request('evento_id'))
                                    No se encontraron equipos en el evento seleccionado.
                                @else
                                    No hay equipos buscando integrantes en este momento.
                                @endif
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('participante.equipos.create') }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Crear mi propio Equipo
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
