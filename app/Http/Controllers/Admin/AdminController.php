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

        // --- LOGICA DE WIDGETS Y PREFERENCIAS ---
        $defaultWidgets = [
            ['key' => 'stats_users', 'position' => 0, 'is_visible' => true, 'settings' => []],
            ['key' => 'stats_equipos', 'position' => 1, 'is_visible' => true, 'settings' => []],
            ['key' => 'stats_proyectos', 'position' => 2, 'is_visible' => true, 'settings' => []],
            ['key' => 'stats_eventos', 'position' => 3, 'is_visible' => true, 'settings' => []],
            ['key' => 'chart_evaluacion', 'position' => 4, 'is_visible' => true, 'settings' => ['type' => 'bar']],
            ['key' => 'chart_carreras', 'position' => 5, 'is_visible' => true, 'settings' => ['type' => 'doughnut']],
            ['key' => 'list_eventos', 'position' => 6, 'is_visible' => true, 'settings' => []],
            // Nuevo Widget: Proyectos Pendientes por Año (Ejemplo)
            ['key' => 'chart_pendientes_anual', 'position' => 7, 'is_visible' => false, 'settings' => ['type' => 'line']],
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
            'pendientesAnual'
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
        // Recopilar todos los datos necesarios (Misma lógica que index)
        $total_jueces = User::whereHas('roles', fn($q) => $q->where('nombre', 'Juez'))->count();
        $total_participantes = User::whereHas('roles', fn($q) => $q->where('nombre', 'Participante'))->count();
        $total_equipos = Equipo::count();
        $total_proyectos = Proyecto::count();
        $eventos_activos = Evento::where('fecha_fin', '>=', now())->orderBy('fecha_inicio', 'asc')->get();

        $participantesPorCarrera = DB::table('participantes')
            ->join('carreras', 'participantes.carrera_id', '=', 'carreras.id')
            ->select('carreras.nombre', DB::raw('count(*) as total'))
            ->groupBy('carreras.nombre')
            ->pluck('total', 'nombre');

        $proyectosEvaluados = Proyecto::has('calificaciones')->count();
        $proyectosPendientes = $total_proyectos - $proyectosEvaluados;

        // Renderizar PDF
        $pdf = Pdf::loadView('admin.reports.dashboard', compact(
            'total_jueces',
            'total_participantes',
            'total_equipos',
            'total_proyectos',
            'eventos_activos',
            'participantesPorCarrera',
            'proyectosEvaluados',
            'proyectosPendientes'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('Reporte_Dashboard_' . now()->format('Y-m-d') . '.pdf');
    }
}
