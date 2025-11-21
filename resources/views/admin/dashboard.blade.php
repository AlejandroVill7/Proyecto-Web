<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Administración') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6 border-b border-gray-200">
                        <div class="text-gray-400 text-xs uppercase font-bold mb-2">Gestión</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Usuarios</h3>
                        <p class="text-sm text-gray-500 mb-4">Jueces, Participantes y Admins.</p>
                        <a href="#" class="text-indigo-600 text-sm font-semibold hover:underline">Administrar &rarr;</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6 border-b border-gray-200">
                        <div class="text-gray-400 text-xs uppercase font-bold mb-2">Gestión</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Equipos</h3>
                        <p class="text-sm text-gray-500 mb-4">Asignar miembros y revisar perfiles.</p>
                        <a href="#" class="text-indigo-600 text-sm font-semibold hover:underline">Administrar &rarr;</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6 border-b border-gray-200">
                        <div class="text-gray-400 text-xs uppercase font-bold mb-2">Configuración</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Eventos</h3>
                        <p class="text-sm text-gray-500 mb-4">Crear eventos y criterios.</p>
                        <a href="#" class="text-indigo-600 text-sm font-semibold hover:underline">Administrar &rarr;</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6 border-b border-gray-200">
                        <div class="text-gray-400 text-xs uppercase font-bold mb-2">Resultados</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Constancias</h3>
                        <p class="text-sm text-gray-500 mb-4">Generar PDFs y ver ganadores.</p>
                        <a href="#" class="text-indigo-600 text-sm font-semibold hover:underline">Ver Reportes &rarr;</a>
                    </div>
                </div>

                <div class="text-2xl font-bold">{{ $total_jueces + $total_participantes }}</div>
                <p class="text-sm text-gray-500 mb-4">
                    {{ $total_jueces }} Jueces, {{ $total_participantes }} Participantes.
                </p>

                <div class="text-2xl font-bold">{{ $total_equipos }}</div>
            </div>

        </div>
    </div>
</x-app-layout>