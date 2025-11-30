<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestión de Equipo') }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.equipos.edit', $equipo) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Renombrar
                </a>
                <a href="{{ route('admin.equipos.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Preparar datos para AlpineJS (Igual que antes, solo asegúrate de pasar los datos correctos desde el controlador) --}}
    @php
        $participantsData = $todos_participantes->map(function($p) use ($equipo) {
            $equipoActual = $p->equipos->first(); 
            return [
                'id' => $p->id,
                'name' => $p->user->name,
                'no_control' => $p->no_control ?? 'S/N',
                'carrera' => $p->carrera->nombre ?? 'N/A',
                'has_team' => $p->equipos->isNotEmpty(),
                'team_name' => $equipoActual ? $equipoActual->nombre : '',
                'in_this_team' => $p->equipos->contains($equipo->id),
            ];
        });
    @endphp

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen" x-data="teamManager({{ $participantsData->toJson() }})">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 text-green-700 dark:text-green-300 text-sm font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 text-red-700 dark:text-red-300 text-sm font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- COLUMNA IZQUIERDA: Buscador Inteligente --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-visible h-fit sticky top-8">
                        
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/20 rounded-t-2xl">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide mb-4">Agregar Miembro</h3>

                            {{-- 1. Formulario de Confirmación (Aparece al seleccionar) --}}
                            <div x-show="selectedId" x-transition class="mb-4 bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-xl border border-indigo-100 dark:border-indigo-800 relative">
                                <button @click="resetSelection()" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                
                                <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-wider mb-1">Candidato Seleccionado</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white mb-2" x-text="selectedName"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 font-mono bg-white dark:bg-gray-800 px-2 py-1 rounded inline-block">ID: <span x-text="selectedControl"></span></p>

                                <form action="{{ route('admin.equipos.miembros.store', $equipo) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="participante_id" x-model="selectedId">
                                    
                                    <div class="mb-3">
                                        <label for="perfil_id" class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Asignar Rol</label>
                                        <select name="perfil_id" class="w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-600 text-gray-900 dark:text-white text-xs py-2 focus:ring-indigo-500" required>
                                            <option value="">Seleccionar...</option>
                                            @foreach($perfiles as $perfil)
                                                <option value="{{ $perfil->id }}">{{ $perfil->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold uppercase tracking-widest shadow-md hover:shadow-lg transition-all">
                                        Confirmar
                                    </button>
                                </form>
                            </div>

                            {{-- 2. Buscador --}}
                            <div class="relative">
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Buscar Alumno</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" x-model="search" placeholder="Nombre o No. Control..." 
                                           class="w-full pl-10 pr-4 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                                </div>
                                
                                {{-- Dropdown Resultados --}}
                                <div x-show="search.length > 0 && filteredParticipants.length > 0" 
                                     class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl max-h-60 overflow-y-auto"
                                     style="display: none;"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100">
                                    
                                    <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                                        <template x-for="p in filteredParticipants" :key="p.id">
                                            <li class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer group" 
                                                :class="{'opacity-50 cursor-not-allowed': p.in_this_team}"
                                                @click="!p.in_this_team && !p.has_team ? selectParticipant(p) : null">
                                                
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <div class="text-sm font-bold text-gray-800 dark:text-gray-200 group-hover:text-indigo-600" x-text="p.name"></div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5" x-text="p.no_control"></div>
                                                        <div class="text-[10px] text-gray-400 uppercase mt-1" x-text="p.carrera"></div>
                                                    </div>
                                                    
                                                    {{-- Etiquetas de Estado --}}
                                                    <div>
                                                        <template x-if="p.in_this_team">
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">YA ESTÁ</span>
                                                        </template>
                                                        <template x-if="!p.in_this_team && p.has_team">
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600 border border-red-200" title="Ocupado">OCUPADO</span>
                                                        </template>
                                                        <template x-if="!p.in_this_team && !p.has_team">
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 border border-green-200 group-hover:bg-green-200">LIBRE</span>
                                                        </template>
                                                    </div>
                                                </div>
                                                
                                                {{-- Feedback visual de equipo ocupado --}}
                                                <div x-show="!p.in_this_team && p.has_team" class="text-[10px] text-red-400 mt-1 text-right">
                                                    En: <span class="font-bold" x-text="p.team_name"></span>
                                                </div>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                {{-- Mensaje No Encontrado --}}
                                <div x-show="search.length > 0 && filteredParticipants.length === 0" class="mt-2 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-center border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">No se encontraron alumnos.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: Miembros y Proyecto --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- 1. Tarjeta de Integrantes --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-700/20">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Equipo Actual</h3>
                            <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 text-xs font-bold px-3 py-1 rounded-full border border-indigo-200 dark:border-indigo-800">
                                {{ $equipo->participantes->count() }} / 5
                            </span>
                        </div>
                        
                        <div class="p-6">
                            @forelse($equipo->participantes as $participante)
                                <div class="flex items-center justify-between p-4 mb-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl hover:shadow-md hover:border-indigo-100 dark:hover:border-indigo-900 transition-all group">
                                    
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                            {{ substr($participante->user->name, 0, 1) }}
                                        </div>
                                        
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $participante->user->name }}</h4>
                                                @if($participante->pivot->perfil_id == 3 || $participante->pivot->es_lider) 
                                                    <span class="text-[10px] bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">Líder</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5">{{ $participante->no_control }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[10px] bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded">{{ $participante->carrera->nombre ?? 'N/A' }}</span>
                                                <span class="text-xs text-indigo-600 dark:text-indigo-400 font-bold">
                                                    {{ \App\Models\Perfil::find($participante->pivot->perfil_id)->nombre ?? 'Sin Rol' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <form action="{{ route('admin.equipos.miembros.destroy', [$equipo, $participante]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all opacity-0 group-hover:opacity-100" title="Eliminar del equipo" onclick="return confirm('¿Estás seguro de expulsar a este miembro?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="text-center py-8 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                                    <p class="text-gray-400 text-sm">Este equipo aún no tiene integrantes.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 2. Tarjeta de Proyecto --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Datos del Proyecto</h3>
                        </div>
                        <div class="p-6">
                            @if($equipo->proyecto)
                                <h4 class="text-xl font-bold text-indigo-700 dark:text-indigo-400 mb-2">{{ $equipo->proyecto->nombre }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed mb-4">{{ $equipo->proyecto->descripcion }}</p>
                                
                                <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <div class="p-2 bg-white dark:bg-gray-800 rounded shadow-sm">
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Evento Asociado</span>
                                        <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $equipo->proyecto->evento->nombre ?? 'Evento no disponible' }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                    <h4 class="text-gray-900 dark:text-white font-bold">Sin Proyecto Registrado</h4>
                                    <p class="text-sm text-gray-500 mt-1">El líder del equipo debe registrar el proyecto desde su panel.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script Alpine (Sin cambios lógicos) --}}
    <script>
        function teamManager(participantsData) {
            return {
                search: '',
                participants: participantsData,
                selectedId: null,
                selectedName: '',
                selectedControl: '',

                get filteredParticipants() {
                    if (this.search === '') return [];
                    const query = this.search.toLowerCase();
                    return this.participants.filter(p => p.name.toLowerCase().includes(query) || p.no_control.toLowerCase().includes(query));
                },
                selectParticipant(p) {
                    this.selectedId = p.id;
                    this.selectedName = p.name;
                    this.selectedControl = p.no_control;
                    this.search = '';
                },
                resetSelection() {
                    this.selectedId = null;
                    this.selectedName = '';
                    this.selectedControl = '';
                }
            }
        }
    </script>
</x-app-layout>