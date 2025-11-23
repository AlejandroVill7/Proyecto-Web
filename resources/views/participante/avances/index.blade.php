<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Bitácora del Proyecto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <div class="md:col-span-1">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Registrar Avance</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                            Documenta qué han logrado hoy. Esto ayuda a los jueces a ver la evolución de su trabajo.
                        </p>

                        <form method="POST" action="{{ route('participante.avances.store') }}">
                            @csrf
                            
                            <div class="mb-4">
                                <x-input-label for="fecha_display" :value="__('Fecha')" />
                                <input type="text" disabled value="{{ now()->format('d/m/Y') }}" 
                                       class="block mt-1 w-full bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-500">
                            </div>

                            <div class="mb-4">
                                <x-input-label for="descripcion" :value="__('Descripción del avance')" />
                                <textarea id="descripcion" name="descripcion" rows="6" 
                                          class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm text-sm" 
                                          placeholder="Ej: Hoy completamos la conexión con la API y diseñamos la vista de usuarios..." required></textarea>
                                <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                            </div>

                            <x-primary-button class="w-full justify-center">
                                {{ __('Publicar en Bitácora') }}
                            </x-primary-button>
                        </form>
                        
                        <div class="mt-6 border-t dark:border-gray-700 pt-4">
                            <a href="{{ route('participante.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-900 dark:hover:text-gray-200 underline">
                                &larr; Volver al Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6">Historial de Evolución</h3>

                        @if($avances->count() > 0)
                            <div class="relative border-l-2 border-gray-200 dark:border-gray-700 ml-3 space-y-8">
                                @foreach($avances as $avance)
                                    <div class="relative pl-8 group">
                                        <div class="absolute -left-[9px] top-0 w-4 h-4 bg-indigo-600 rounded-full border-4 border-white dark:border-gray-800"></div>
                                        
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                                                    {{ \Carbon\Carbon::parse($avance->fecha)->isoFormat('dddd D [de] MMMM, YYYY') }}
                                                </span>
                                                <span class="text-xs text-gray-400 ml-2">
                                                    {{ $avance->created_at->format('H:i A') }}
                                                </span>
                                            </div>
                                            
                                            <form action="{{ route('participante.avances.destroy', $avance->id) }}" method="POST" onsubmit="return confirm('¿Borrar este registro?');" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                                @csrf @method('DELETE')
                                                <button class="text-red-400 hover:text-red-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-gray-700 dark:text-gray-300 text-sm leading-relaxed border border-gray-100 dark:border-gray-600">
                                            {{ $avance->descripcion }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">Aún no hay avances registrados.</p>
                                <p class="text-gray-400 text-xs">¡Sé el primero en subir una actualización!</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>