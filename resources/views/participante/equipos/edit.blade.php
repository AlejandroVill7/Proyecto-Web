<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Equipo') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="teamManager({{ $candidatos }})">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1 space-y-6">
                    
                    @php
                        $totalMiembros = $equipo->participantes->count();
                        $carreras = $equipo->participantes->pluck('carrera_id')->unique();
                        $esMultidisciplinario = $carreras->count() > 1;
                    @endphp
                    
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 border-l-4 {{ $totalMiembros >= 2 && $esMultidisciplinario ? 'border-green-500' : 'border-yellow-500' }}">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase">Estado del Equipo</h3>
                            <span class="text-xs font-bold px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
                                {{ $totalMiembros }}/5
                            </span>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs flex items-center {{ $totalMiembros >= 2 ? 'text-green-600' : 'text-gray-500' }}">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Mínimo 2 integrantes
                            </p>
                            <p class="text-xs flex items-center {{ $esMultidisciplinario ? 'text-green-600' : 'text-gray-500' }}">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Multidisciplinario
                            </p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg p-5">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-4 uppercase border-b dark:border-gray-700 pb-2">
                            Agregar Integrante
                        </h3>

                        @if($totalMiembros < 5)
                            <div class="relative" x-data="{ open: false }">
                                <label class="text-xs text-gray-500 mb-1 block">BUSCAR ALUMNO</label>
                                <div class="relative">
                                    <input type="text" 
                                           x-model="search" 
                                           @focus="open = true"
                                           @click.away="open = false"
                                           placeholder="Escribe el nombre..." 
                                           class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-600 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500 pl-4 py-2">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    </div>
                                </div>
                                
                                <div x-show="search.length > 0 && filteredParticipants.length > 0" 
                                     class="absolute z-50 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md shadow-xl max-h-60 overflow-y-auto mt-2"
                                     style="display: none;"
                                     x-transition>
                                    <template x-for="p in filteredParticipants" :key="p.id">
                                        <div @click="selectParticipant(p); open = false" 
                                             class="p-3 hover:bg-indigo-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                            <div>
                                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200" x-text="p.name"></p>
                                                <p class="text-[10px] text-gray-500" x-text="p.carrera"></p>
                                            </div>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800">Disponible</span>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="search.length > 0 && filteredParticipants.length === 0" class="absolute z-50 w-full bg-white dark:bg-gray-800 p-2 text-xs text-gray-500 border rounded shadow mt-1 text-center">Sin resultados</div>
                            </div>

                            <form method="POST" action="{{ route('participante.equipos.addMember') }}" x-show="selectedId !== null" class="mt-4 bg-indigo-50 dark:bg-indigo-900/20 p-3 rounded-lg border border-indigo-100 dark:border-indigo-800">
                                @csrf
                                <input type="hidden" name="participante_id" x-model="selectedId">
                                
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <p class="text-xs text-indigo-500 font-bold uppercase">Seleccionado:</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="selectedName"></p>
                                    </div>
                                    <button type="button" @click="resetSelection()" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <div class="mb-3">
                                    <label class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Asignar Rol</label>
                                    <select name="perfil_id" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-xs py-1.5">
                                        @foreach($perfiles as $perfil)
                                            <option value="{{ $perfil->id }}">{{ $perfil->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <x-primary-button class="w-full justify-center text-xs">Invitar al Equipo</x-primary-button>
                            </form>
                        @else
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded text-center text-xs text-yellow-700 dark:text-yellow-400">
                                Equipo completo (5/5).
                            </div>
                        @endif
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-5">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-4 uppercase border-b dark:border-gray-700 pb-2">
                            Integrantes
                        </h3>
                        <div class="space-y-3">
                            @foreach($equipo->participantes as $miembro)
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <div class="w-8 h-8 flex-shrink-0 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-xs">
                                            {{ substr($miembro->user->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-900 dark:text-gray-100 truncate">{{ $miembro->user->name }}</p>
                                            <p class="text-[10px] text-gray-500 truncate">
                                                {{ $miembro->carrera->nombre ?? 'N/A' }} • 
                                                <span class="text-indigo-500">
                                                    {{ \App\Models\Perfil::find($miembro->pivot->perfil_id)->nombre ?? 'Rol' }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    @if(Auth::user()->participante->id !== $miembro->id)
                                        <form action="{{ route('participante.equipos.removeMember', $miembro->id) }}" method="POST" onsubmit="return confirm('¿Sacar del equipo?');">
                                            @csrf @method('DELETE')
                                            <button class="text-gray-400 hover:text-red-500 transition opacity-0 group-hover:opacity-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-green-600 font-bold bg-green-50 px-1 rounded">Tú</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6 border-b dark:border-gray-700 pb-4">
                                Configuración Principal
                            </h3>

                            @if(session('success'))
                                <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800 font-medium">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800 font-medium">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('participante.equipos.update', $equipo->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <x-input-label for="nombre" :value="__('Nombre del Equipo')" />
                                        <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre', $equipo->nombre)" required />
                                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                                        <p class="text-xs text-blue-500 mt-2 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Verificaremos disponibilidad al guardar.
                                        </p>
                                    </div>

                                    <div>
                                        <x-input-label for="nombre_proyecto" :value="__('Título del Proyecto')" />
                                        <x-text-input id="nombre_proyecto" class="block mt-1 w-full" type="text" name="nombre_proyecto" :value="old('nombre_proyecto', $equipo->proyecto->nombre ?? '')" required />
                                        <x-input-error :messages="$errors->get('nombre_proyecto')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <x-input-label for="repositorio_url" :value="__('Repositorio (GitHub/GitLab)')" />
                                    <x-text-input id="repositorio_url" class="block mt-1 w-full" type="url" name="repositorio_url" :value="old('repositorio_url', $equipo->proyecto->repositorio_url ?? '')" placeholder="https://..." />
                                    <x-input-error :messages="$errors->get('repositorio_url')" class="mt-2" />
                                </div>

                                <div class="mb-6">
                                    <x-input-label for="descripcion_proyecto" :value="__('Descripción del Proyecto')" />
                                    <textarea id="descripcion_proyecto" name="descripcion_proyecto" rows="5" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('descripcion_proyecto', $equipo->proyecto->descripcion ?? '') }}</textarea>
                                    <x-input-error :messages="$errors->get('descripcion_proyecto')" class="mt-2" />
                                </div>

                                <div class="flex items-center justify-end pt-4 border-t dark:border-gray-700">
                                    <x-primary-button class="px-6 py-3 text-base">
                                        {{ __('GUARDAR CAMBIOS') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function teamManager(participantsData) {
            return {
                search: '',
                participants: participantsData,
                selectedId: null,
                selectedName: '',

                get filteredParticipants() {
                    if (this.search === '') return [];
                    const query = this.search.toLowerCase();
                    return this.participants.filter(p => p.name.toLowerCase().includes(query));
                },
                selectParticipant(p) {
                    this.selectedId = p.id;
                    this.selectedName = p.name;
                    this.search = '';
                },
                resetSelection() {
                    this.selectedId = null;
                    this.selectedName = '';
                }
            }
        }
    </script>
</x-app-layout>