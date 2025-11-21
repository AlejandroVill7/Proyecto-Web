<?php

namespace App\Http\Controllers\Juez;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Equipo, Evento, Proyecto};
use Illuminate\Support\Facades\Auth;


class JuezController extends Controller
{
    public function index()
    {
        $juez_id = Auth::id();

        // Obtenemos los proyectos que pertenecen a eventos activos
        $proyectos = Proyecto::whereHas('evento', function($query) {
                // Filtramos solo eventos que no han terminado
                $query->where('fecha_fin', '>=', now());
            })
            // Eager Loading: Cargamos equipo y evento para no hacer mil consultas en la vista
            ->with(['equipo', 'evento'])
            // Cargamos las calificaciones PERO solo las de este juez
            ->with(['calificaciones' => function($query) use ($juez_id) {
                $query->where('juez_user_id', $juez_id);
            }])
            ->get();

        // Procesamos para agregar un atributo "estado" rápido para la vista
        $proyectos->transform(function ($proyecto) {
            // Si tiene calificaciones, asumimos que ya fue evaluado (o parcialmente)
            $proyecto->estado_evaluacion = $proyecto->calificaciones->isNotEmpty() ? 'Calificado' : 'Pendiente';
            return $proyecto;
        });

        return view('juez.dashboard', compact('proyectos'));
    }
}
