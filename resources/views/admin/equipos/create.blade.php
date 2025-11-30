<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Crear Nuevo Equipo') }}
            </h2>
            <a href="{{ route('admin.equipos.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden relative">
                
                {{-- Decoración Superior --}}
                <div class="h-1.5 w-full bg-gradient-to-r from-indigo-500 to-purple-600"></div>

                <div class="p-8">
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Registrar Equipo Manualmente</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Crea un grupo vacío para asignar estudiantes posteriormente.</p>
                    </div>

                    <form method="POST" action="{{ route('admin.equipos.store') }}" class="space-y-6">
                        @csrf

                        {{-- Nombre del Equipo --}}
                        <div>
                            <x-input-label for="nombre" value="Nombre del Equipo" class="mb-2 font-bold" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    {{-- Icono Users --}}
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                </div>
                                <x-text-input id="nombre" class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm" 
                                    type="text" name="nombre" :value="old('nombre')" required autofocus placeholder="Ej. Alpha Team, Los Innovadores..." />
                            </div>
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        {{-- Alerta Informativa --}}
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl p-4 flex gap-3 items-start">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <h4 class="text-xs font-bold text-blue-700 dark:text-blue-300 uppercase mb-1">Siguiente Paso</h4>
                                <p class="text-xs text-blue-600 dark:text-blue-400 leading-relaxed">
                                    Al crear el equipo, serás redirigido automáticamente a la pantalla de <strong>Gestión de Miembros</strong> para agregar a los integrantes y asignar roles.
                                </p>
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('admin.equipos.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-500/30 transform hover:-translate-y-0.5 transition-all duration-200">
                                <span class="mr-2">Crear y Asignar</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>