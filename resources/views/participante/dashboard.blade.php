<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel del Participante') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    Hola, <strong>{{ Auth::user()->nombre }}</strong>. Estás participando en: <span class="text-indigo-600">{{ $evento_actual->nombre ?? 'Ningún evento activo' }}</span>
                </div>
            </div>

            @if(Auth::user()->participante && Auth::user()->participante->equipos->count() > 0)
                {{-- CASO 1: TIENE EQUIPO --}}
                @php $equipo = Auth::user()->participante->equipos->first(); @endphp
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <h3 class="text-lg font-bold text-gray-700 mb-2">Tu Equipo: {{ $equipo->nombre }}</h3>
                        <p class="text-sm text-gray-500 mb-4">Proyecto: {{ $equipo->proyecto->nombre ?? 'Sin definir' }}</p>
                        
                        <div class="mt-4">
                            <h4 class="text-xs font-uppercase text-gray-400 font-bold">Miembros:</h4>
                            <ul class="list-disc list-inside text-sm mt-2">
                                @foreach($equipo->participantes as $miembro)
                                    <li>{{ $miembro->user->nombre }} ({{ $miembro->pivot->perfil->nombre ?? 'Rol' }})</li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <div class="mt-6 flex space-x-3">
                            <a href="#" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 text-sm">Gestionar Equipo</a>
                            <a href="#" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 text-sm">Subir Avance</a>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-700 mb-4">Avance del Proyecto</h3>
                        <div class="h-40 bg-gray-100 rounded flex items-center justify-center text-gray-400">
                            [ Gráfico de Barras Aquí ]
                        </div>
                        <p class="text-xs text-center mt-2 text-gray-500">Basado en criterios evaluados</p>
                    </div>
                </div>

            @else
                {{-- CASO 2: NO TIENE EQUIPO --}}
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Aún no estás inscrito en ningún equipo. Necesitas un equipo para participar.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow text-center">
                        <h3 class="text-lg font-bold mb-2">¿Ya tienes compañeros?</h3>
                        <p class="text-sm text-gray-500 mb-4">Crea un nuevo equipo multidisciplinario.</p>
                        <a href="#" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500">Crear Equipo</a>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow text-center">
                        <h3 class="text-lg font-bold mb-2">¿Buscas equipo?</h3>
                        <p class="text-sm text-gray-500 mb-4">Mira la lista de equipos con vacantes.</p>
                        <a href="#" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">Ver Equipos</a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>