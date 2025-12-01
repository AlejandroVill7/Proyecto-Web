<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Equipo;
use App\Models\Evento;
use App\Models\Proyecto;
use App\Models\DashboardPreference;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    /**
     * Muestra el panel de administración con métricas y gráficos.
     * 
     * DATOS PARA GRÁFICO 2: Estado de Evaluación de Proyectos (Barras)
     * Contamos cuántos proyectos tienen calificaciones vs cuántos no.
     */
    public function index()
    {
        $total_jueces = User::whereHas('roles', fn($q) => $q->where('nombre', 'Juez'))->count();
        $total_participantes = User::whereHas('roles', fn($q) => $q->where('nombre', 'Participante'))->count();
        $total_equipos = Equipo::count();
        $total_proyectos = Proyecto::count();

        $eventos_activos = Evento::where('fecha_fin', '>=', now())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        $participantesPorCarrera = DB::table('participantes')
            ->join('carreras', 'participantes.carrera_id', '=', 'carreras.id')
            ->select('carreras.nombre', DB::raw('count(*) as total'))
            ->groupBy('carreras.nombre')
            ->pluck('total', 'nombre');

        $proyectosEvaluados = Proyecto::has('calificaciones')->count();
        $proyectosPendientes = $total_proyectos - $proyectosEvaluados;

        // Datos por Evento (Para el selector de evaluación)
        $eventos_stats = Evento::withCount([
            'proyectos',
            'proyectos as evaluados_count' => function ($query) {
                $query->has('calificaciones');
            }
        ])->get()->map(function ($evento) {
            return [
                'id' => $evento->id,
                'nombre' => $evento->nombre,
                'total' => $evento->proyectos_count,
                'evaluados' => $evento->evaluados_count,
                'pendientes' => $evento->proyectos_count - $evento->evaluados_count
            ];
        });

        // Nuevo Widget: Proyectos por Categoría (Si existe relación, si no, usamos Carreras como proxy o simulamos)
        $categoriasData = $participantesPorCarrera; // Reutilizamos para ejemplo

        // --- LOGICA DE WIDGETS Y PREFERENCIAS ---
        $defaultWidgets = [
            ['key' => 'stats_users', 'position' => 0, 'is_visible' => true, 'settings' => []],
            ['key' => 'stats_equipos', 'position' => 1, 'is_visible' => true, 'settings' => []],
            ['key' => 'stats_proyectos', 'position' => 2, 'is_visible' => true, 'settings' => []],
            ['key' => 'stats_eventos', 'position' => 3, 'is_visible' => true, 'settings' => []],
            ['key' => 'chart_evaluacion', 'position' => 4, 'is_visible' => true, 'settings' => ['type' => 'bar', 'event_id' => null]],
            ['key' => 'chart_carreras', 'position' => 5, 'is_visible' => true, 'settings' => ['type' => 'doughnut']],
            ['key' => 'list_eventos', 'position' => 6, 'is_visible' => true, 'settings' => []],
            ['key' => 'chart_pendientes_anual', 'position' => 7, 'is_visible' => false, 'settings' => ['type' => 'line']],
            ['key' => 'chart_categorias', 'position' => 8, 'is_visible' => false, 'settings' => ['type' => 'bar']],
        ];

        // Obtener preferencias del usuario
        $userPreferences = DashboardPreference::where('user_id', Auth::id())->get()->keyBy('widget_key');

        // Fusionar preferencias con defaults
        $widgets = collect($defaultWidgets)->map(function ($default) use ($userPreferences) {
            if ($userPreferences->has($default['key'])) {
                $pref = $userPreferences->get($default['key']);
                return [
                    'key' => $default['key'],
                    'position' => $pref->position,
                    'is_visible' => $pref->is_visible,
                    'settings' => array_merge($default['settings'], $pref->settings ?? []),
                ];
            }
            return $default;
        })->sortBy('position')->values();

        // Datos adicionales para el nuevo widget (Proyectos Pendientes por Año - Simulado o Real)
        // Aquí simularemos datos de los últimos 5 años para el ejemplo
        $pendientesAnual = [
            '2021' => 12,
            '2022' => 8,
            '2023' => 15,
            '2024' => 5,
            '2025' => $proyectosPendientes // El actual
        ];

        return view('admin.dashboard', compact(
            'total_jueces',
            'total_participantes',
            'total_equipos',
            'eventos_activos',
            'total_proyectos',
            'participantesPorCarrera',
            'proyectosEvaluados',
            'proyectosPendientes',
            'widgets',
            'pendientesAnual',
            'eventos_stats',
            'categoriasData'
        ));
    }

    public function savePreferences(Request $request)
    {
        $request->validate([
            'widgets' => 'required|array',
            'widgets.*.key' => 'required|string',
            'widgets.*.position' => 'required|integer',
            'widgets.*.is_visible' => 'required|boolean',
            'widgets.*.settings' => 'nullable|array',
        ]);

        $user = Auth::user();

        foreach ($request->widgets as $widgetData) {
            DashboardPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'widget_key' => $widgetData['key']
                ],
                [
                    'position' => $widgetData['position'],
                    'is_visible' => $widgetData['is_visible'],
                    'settings' => $widgetData['settings'] ?? []
                ]
            );
        }

        return response()->json(['message' => 'Preferencias guardadas correctamente.']);
    }

    public function generateReport()
    {
        $total_jueces = User::whereHas('roles', fn($q) => $q->where('nombre', 'Juez'))->count();
        $total_participantes = User::whereHas('roles', fn($q) => $q->where('nombre', 'Participante'))->count();
        $total_equipos = Equipo::count();
        $total_proyectos = Proyecto::count();

        $eventos_activos = Evento::where('fecha_fin', '>=', now())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        $participantesPorCarrera = DB::table('participantes')
            ->join('carreras', 'participantes.carrera_id', '=', 'carreras.id')
            ->select('carreras.nombre', DB::raw('count(*) as total'))
            ->groupBy('carreras.nombre')
            ->pluck('total', 'nombre');

        $proyectosEvaluados = Proyecto::has('calificaciones')->count();
        $proyectosPendientes = $total_proyectos - $proyectosEvaluados;

        // Obtener preferencias para saber qué mostrar
        $userPreferences = DashboardPreference::where('user_id', Auth::id())->get()->keyBy('widget_key');

        // Lógica específica para el reporte basada en preferencias
        $evaluacionSettings = $userPreferences->get('chart_evaluacion')?->settings ?? [];
        $eventoReporte = null;
        $statsReporte = [
            'evaluados' => $proyectosEvaluados,
            'pendientes' => $proyectosPendientes,
            'total' => $total_proyectos
        ];

        if (!empty($evaluacionSettings['event_id'])) {
            $evento = Evento::withCount([
                'proyectos',
                'proyectos as evaluados_count' => function ($query) {
                    $query->has('calificaciones');
                }
            ])->find($evaluacionSettings['event_id']);

            if ($evento) {
                $eventoReporte = $evento->nombre;
                $statsReporte = [
                    'evaluados' => $evento->evaluados_count,
                    'pendientes' => $evento->proyectos_count - $evento->evaluados_count,
                    'total' => $evento->proyectos_count
                ];
            }
        }

        // Filter data based on visibility (Optional: if user wants to hide sections in PDF)
        // For now, we pass flags to the view
        $visibleSections = [
            'stats' => $userPreferences->get('stats_users')?->is_visible ?? true, // Assuming if one stat is visible, show stats section
            'evaluacion' => $userPreferences->get('chart_evaluacion')?->is_visible ?? true,
            'carreras' => $userPreferences->get('chart_carreras')?->is_visible ?? true,
            'eventos' => $userPreferences->get('list_eventos')?->is_visible ?? true,
        ];

        // Renderizar PDF
        $pdf = Pdf::loadView('admin.reports.dashboard', compact(
            'total_jueces',
            'total_participantes',
            'total_equipos',
            'total_proyectos',
            'eventos_activos',
            'participantesPorCarrera',
            'proyectosEvaluados',
            'proyectosPendientes',
            'eventoReporte',
            'statsReporte',
            'visibleSections'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('Reporte_Dashboard_' . now()->format('Y-m-d') . '.pdf');
    }
}
