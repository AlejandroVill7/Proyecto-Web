<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Explorar Equipos') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. BARRA DE FILTRO ESTILIZADA --}}
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Buscar Vacantes
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Encuentra un equipo que necesite tu perfil técnico.</p>
                </div>
                
                <form method="GET" action="{{ route('participante.equipos.join') }}" class="w-full md:w-auto">
                    <div class="relative">
                        <select name="evento_id" onchange="this.form.submit()"
                            class="w-full md:w-64 pl-4 pr-10 py-2.5 bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow cursor-pointer appearance-none">
                            <option value="">Todos los eventos activos</option>
                            @foreach ($eventos as $evento)
                                <option value="{{ $evento->id }}" {{ request('evento_id') == $evento->id ? 'selected' : '' }}>
                                    {{ $evento->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </form>
            </div>

            {{-- MENSAJES DE ERROR --}}
            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-xl dark:bg-red-900/50 dark:text-red-300 border border-red-200 dark:border-red-800 flex items-center gap-2" role="alert">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- 2. GRID DE TARJETAS MODERNAS --}}
            @if ($equiposDisponibles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($equiposDisponibles as $equipo)
                        <div class="group relative bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full overflow-hidden">
                            
                            <div class="h-1.5 w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

                            <div class="p-6 flex-1 flex flex-col">
                                {{-- Header de la Card --}}
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="font-bold text-lg text-gray-900 dark:text-white leading-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            {{ $equipo->nombre }}
                                        </h4>
                                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                            {{ $equipo->proyecto->evento->nombre ?? 'Evento' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded-md border border-blue-100 dark:border-blue-800">
                                        <svg class="w-3 h-3 text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        <span class="text-xs font-bold text-blue-700 dark:text-blue-300">{{ $equipo->participantes_count }}/5</span>
                                    </div>
                                </div>

                                {{-- Cuerpo del Proyecto --}}
                                <div class="mb-4">
                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1 flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                        {{ $equipo->proyecto->nombre ?? 'Sin Proyecto' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed">
                                        {{ $equipo->proyecto->descripcion ?? 'El equipo no ha añadido una descripción pública del proyecto.' }}
                                    </p>
                                </div>
                                
                                {{-- Espaciador para empujar el footer --}}
                                <div class="mt-auto"></div>
                            </div>

                            {{-- Footer: Formulario de Acción --}}
                            <div class="bg-gray-50 dark:bg-gray-700/30 px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                                <form method="POST" action="{{ route('participante.equipos.join.store') }}">
                                    @csrf
                                    <input type="hidden" name="equipo_id" value="{{ $equipo->id }}">

                                    <button type="submit" class="w-full flex justify-center items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                                        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                        Enviar Solicitud
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                {{-- EMPTY STATE --}}
                <div class="flex flex-col items-center justify-center py-16 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 text-center">
                    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-full mb-4">
                        <svg class="h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">No hay equipos disponibles</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm">
                        @if (request('evento_id'))
                            No se encontraron equipos reclutando en el evento seleccionado.
                        @else
                            Parece que no hay vacantes en este momento. ¡Sé el primero en liderar!
                        @endif
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('participante.equipos.create') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest shadow-lg transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Crear mi propio Equipo
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>