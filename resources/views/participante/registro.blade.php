<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Completar Perfil de Participante') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Información Académica Requerida</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Para acceder al panel de eventos y equipos, necesitamos que completes tu registro con tu número de control y teléfono de contacto.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('participante.registro.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="no_control" :value="__('Número de Control / Matrícula')" />
                            <x-text-input id="no_control" class="block mt-1 w-full" 
                                          type="text" 
                                          name="no_control" 
                                          :value="old('no_control', $perfil->no_control ?? '')" 
                                          required autofocus autocomplete="off" />
                            <x-input-error :messages="$errors->get('no_control')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="carrera_id" :value="__('Carrera')" />
                            <select id="carrera_id" name="carrera_id" 
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                <option value="">Selecciona tu carrera</option>
                                @foreach($carreras as $carrera)
                                    <option value="{{ $carrera->id }}" 
                                        {{ old('carrera_id', $perfil->carrera_id ?? '') == $carrera->id ? 'selected' : '' }}>
                                        {{ $carrera->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('carrera_id')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="telefono" :value="__('Teléfono (WhatsApp)')" />
                            <x-text-input id="telefono" class="block mt-1 w-full" 
                                          type="tel" 
                                          name="telefono" 
                                          :value="old('telefono', $perfil->telefono ?? '')" 
                                          placeholder="Ej: 951 123 4567"
                                          required />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Necesario para contacto urgente durante el evento.
                            </p>
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <x-primary-button class="ms-4">
                                {{ __('Guardar y Continuar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>