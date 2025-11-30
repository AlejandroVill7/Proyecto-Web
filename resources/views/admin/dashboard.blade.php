<x-app-layout>
    {{-- Header Actions --}}
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
            {{ __('Dashboard') }}
        </h2>
        <div class="flex items-center gap-3">
            {{-- Generate PDF Button --}}
            <a href="{{ route('admin.dashboard.report') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition shadow-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Generar Reporte PDF
            </a>

            {{-- Settings Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Configurar Widgets
                </button>
                
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-72 rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 z-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-4" style="display: none;">
                    <h3 class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-3">Visibilidad de Widgets</h3>
                    <form id="preferencesForm">
                        <div class="space-y-2 max-h-60 overflow-y-auto">
                            @foreach($widgets as $widget)
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                        <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 widget-visibility" 
                                               data-key="{{ $widget['key'] }}" 
                                               {{ $widget['is_visible'] ? 'checked' : '' }}>
                                        {{ ucwords(str_replace(['_', 'stats', 'chart', 'list'], [' ', '', '', ''], $widget['key'])) }}
                                    </label>
                                    @if(str_contains($widget['key'], 'chart'))
                                        <select class="text-xs rounded border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 py-1 px-2 widget-type" data-key="{{ $widget['key'] }}">
                                            <option value="bar" {{ ($widget['settings']['type'] ?? '') == 'bar' ? 'selected' : '' }}>Barras</option>
                                            <option value="doughnut" {{ ($widget['settings']['type'] ?? '') == 'doughnut' ? 'selected' : '' }}>Dona</option>
                                            <option value="line" {{ ($widget['settings']['type'] ?? '') == 'line' ? 'selected' : '' }}>Línea</option>
                                            <option value="pie" {{ ($widget['settings']['type'] ?? '') == 'pie' ? 'selected' : '' }}>Pastel</option>
                                        </select>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 text-right">
                            <button type="button" id="saveConfigBtn" class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded-md hover:bg-indigo-700 transition">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- CDN de Chart.js y SortableJS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    {{-- Contenedor Principal Grid Sortable --}}
    <div id="dashboard-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($widgets as $widget)
            @if($widget['is_visible'])
                <div class="widget-item relative group {{ str_contains($widget['key'], 'chart') || str_contains($widget['key'], 'list') ? 'col-span-1 md:col-span-2' : 'col-span-1' }}" data-key="{{ $widget['key'] }}">
                    
                    {{-- Drag Handle --}}
                    <div class="drag-handle absolute top-2 right-2 p-1 text-gray-400 hover:text-gray-600 cursor-move opacity-0 group-hover:opacity-100 transition z-10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </div>

                    {{-- WIDGET CONTENT SWITCHER --}}
                    @switch($widget['key'])
                        @case('stats_users')
                            <div class="h-full rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 transition-transform hover:scale-[1.02]">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Usuarios</p>
                                        <h4 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $total_jueces + $total_participantes }}</h4>
                                    </div>
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center gap-2">
                                    <span class="flex items-center gap-1 text-sm font-medium text-green-600 dark:text-green-400">{{ $total_jueces }} Jueces</span>
                                    <span class="text-sm text-gray-400">|</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $total_participantes }} Alumnos</span>
                                </div>
                            </div>
                            @break

                        @case('stats_equipos')
                            <div class="h-full rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 transition-transform hover:scale-[1.02]">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Equipos Activos</p>
                                        <h4 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $total_equipos }}</h4>
                                    </div>
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                </div>
                                <div class="mt-4"><span class="text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-2 py-1 rounded-md">Registrados</span></div>
                            </div>
                            @break

                        @case('stats_proyectos')
                            <div class="h-full rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 transition-transform hover:scale-[1.02]">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Proyectos</p>
                                        <h4 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $total_proyectos }}</h4>
                                    </div>
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-between text-sm">
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ $proyectosEvaluados }} Evaluados</span>
                                    <span class="text-gray-400">{{ $proyectosPendientes }} Pendientes</span>
                                </div>
                            </div>
                            @break

                        @case('stats_eventos')
                            <div class="h-full rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 transition-transform hover:scale-[1.02]">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Eventos Activos</p>
                                        <h4 class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $eventos_activos->count() }}</h4>
                                    </div>
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-50 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                </div>
                                <div class="mt-4"><span class="text-sm font-medium text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/30 px-2 py-1 rounded-md">En curso</span></div>
                            </div>
                            @break

                        @case('chart_evaluacion')
                            <div class="h-full rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Progreso de Evaluación</h3>
                                <div class="relative h-72 w-full">
                                    <canvas id="chartEvaluacion" data-type="{{ $widget['settings']['type'] ?? 'bar' }}"></canvas>
                                </div>
                            </div>
                            @break

                        @case('chart_carreras')
                            <div class="h-full rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Participación por Carrera</h3>
                                <div class="relative h-72 w-full flex justify-center">
                                    <canvas id="chartCarreras" data-type="{{ $widget['settings']['type'] ?? 'doughnut' }}"></canvas>
                                </div>
                            </div>
                            @break

                        @case('chart_pendientes_anual')
                            <div class="h-full rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Proyectos Pendientes (Anual)</h3>
                                <div class="relative h-72 w-full flex justify-center">
                                    <canvas id="chartPendientesAnual" data-type="{{ $widget['settings']['type'] ?? 'line' }}"></canvas>
                                </div>
                            </div>
                            @break

                        @case('list_eventos')
                            <div class="h-full rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Próximos Eventos</h3>
                                    <a href="{{ route('admin.eventos.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Ver todo</a>
                                </div>
                                @if($eventos_activos->isEmpty())
                                    <div class="text-center py-8"><p class="text-gray-500 dark:text-gray-400 text-sm">No hay eventos programados.</p></div>
                                @else
                                    <div class="space-y-4">
                                        @foreach($eventos_activos as $evento)
                                            <div class="group flex items-start gap-4 p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer">
                                                <div class="flex flex-col items-center justify-center h-14 w-14 rounded-lg bg-indigo-50 dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-gray-600">
                                                    <span class="text-xs font-bold uppercase">{{ \Carbon\Carbon::parse($evento->fecha_inicio)->locale('es')->shortMonthName }}</span>
                                                    <span class="text-xl font-bold">{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('d') }}</span>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $evento->nombre }}</h4>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $evento->descripcion }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @break
                    @endswitch
                </div>
            @endif
        @endforeach
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- 1. SORTABLE JS ---
            const grid = document.getElementById('dashboard-grid');
            new Sortable(grid, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: function () {
                    savePreferences();
                }
            });

            // --- 2. CHART CONFIG ---
            const getTextColor = () => document.documentElement.classList.contains('dark') ? '#9ca3af' : '#64748b';
            const getGridColor = () => document.documentElement.classList.contains('dark') ? '#374151' : '#f1f5f9';

            // Chart 1: Evaluación
            const ctxEvaluacion = document.getElementById('chartEvaluacion');
            if (ctxEvaluacion) {
                new Chart(ctxEvaluacion, {
                    type: ctxEvaluacion.dataset.type,
                    data: {
                        labels: ['Evaluados', 'Pendientes'],
                        datasets: [{
                            label: 'Proyectos',
                            data: [{{ $proyectosEvaluados }}, {{ $proyectosPendientes }}],
                            backgroundColor: ['#4f46e5', '#94a3b8'],
                            borderRadius: 6
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }

            // Chart 2: Carreras
            const ctxCarreras = document.getElementById('chartCarreras');
            if (ctxCarreras) {
                new Chart(ctxCarreras, {
                    type: ctxCarreras.dataset.type,
                    data: {
                        labels: @json(array_keys($participantesPorCarrera->toArray())),
                        datasets: [{
                            data: @json(array_values($participantesPorCarrera->toArray())),
                            backgroundColor: ['#6366f1', '#ec4899', '#10b981', '#f59e0b', '#3b82f6'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }

            // Chart 3: Pendientes Anual (Nuevo)
            const ctxPendientes = document.getElementById('chartPendientesAnual');
            if (ctxPendientes) {
                new Chart(ctxPendientes, {
                    type: ctxPendientes.dataset.type,
                    data: {
                        labels: @json(array_keys($pendientesAnual)),
                        datasets: [{
                            label: 'Pendientes',
                            data: @json(array_values($pendientesAnual)),
                            borderColor: '#f59e0b',
                            backgroundColor: '#f59e0b',
                            tension: 0.4
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }

            // --- 3. SAVE PREFERENCES ---
            const saveBtn = document.getElementById('saveConfigBtn');
            if (saveBtn) {
                saveBtn.addEventListener('click', savePreferences);
            }

            function savePreferences() {
                const widgets = [];
                const items = grid.querySelectorAll('.widget-item');
                
                // Get current order from DOM
                items.forEach((item, index) => {
                    const key = item.dataset.key;
                    // Find visibility and type from the form (even if widget is not in DOM, we need to check form state)
                    // But here we iterate DOM, so we only see visible ones. 
                    // Better approach: Iterate the FORM inputs to build the full list, but use DOM index for position of visible ones.
                });

                // Re-approach: Build list from the Configuration Form, updating positions based on DOM.
                const allWidgets = [];
                document.querySelectorAll('.widget-visibility').forEach(input => {
                    const key = input.dataset.key;
                    const isVisible = input.checked;
                    const typeSelect = document.querySelector(`.widget-type[data-key="${key}"]`);
                    const type = typeSelect ? typeSelect.value : null;
                    
                    // Find position in DOM
                    let position = 999;
                    const domItem = document.querySelector(`.widget-item[data-key="${key}"]`);
                    if (domItem) {
                        position = Array.from(grid.children).indexOf(domItem);
                    }

                    allWidgets.push({
                        key: key,
                        position: position,
                        is_visible: isVisible,
                        settings: type ? { type: type } : {}
                    });
                });

                // Sort by position to be safe, though backend handles it
                allWidgets.sort((a, b) => a.position - b.position);

                fetch('{{ route("admin.dashboard.preferences") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ widgets: allWidgets })
                })
                .then(response => response.json())
                .then(data => {
                    // Reload to reflect changes (visibility/types require re-render)
                    window.location.reload();
                })
                .catch(error => console.error('Error:', error));
            }
        });
    </script>
</x-app-layout>