<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Equipo;
use App\Models\Evento;
use App\Models\Proyecto;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Contadores para las tarjetas del Dashboard
        // Usamos whereHas para contar usuarios por su rol específico
        $total_jueces = User::whereHas('roles', fn($q) => $q->where('nombre', 'Juez'))->count();
        $total_participantes = User::whereHas('roles', fn($q) => $q->where('nombre', 'Participante'))->count();
        $total_equipos = Equipo::count();
        
        // 2. Eventos que están ocurriendo ahora o a futuro
        $eventos_activos = Evento::where('fecha_fin', '>=', now())
                                 ->orderBy('fecha_inicio', 'asc')
                                 ->take(5)
                                 ->get();

        return view('admin.dashboard', compact(
            'total_jueces', 
            'total_participantes', 
            'total_equipos', 
            'eventos_activos'
        ));
    }
}
