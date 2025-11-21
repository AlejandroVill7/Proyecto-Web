<?php

namespace App\Http\Controllers\Participante;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Equipo, Evento, Proyecto, Participante};
use Illuminate\Support\Facades\Auth;


class ParticipanteController extends Controller
{
    public function index()
    {
        $user = User::with(['participante.equipos.proyecto', 'participante.equipos.participantes.user'])
                    ->find(Auth::id());

        $participante = $user ? $user->participante : null;
        $equipo = $participante ? $participante->equipos->first() : null;
        
        // Si hay equipo, obtenemos el proyecto
        $proyecto = $equipo ? $equipo->proyecto : null;

        // Buscamos un evento activo para mostrar en la bienvenida
        $evento_actual = Evento::where('fecha_inicio', '<=', now())
                               ->where('fecha_fin', '>=', now())
                               ->first();

        return view('participante.dashboard', compact('equipo', 'proyecto', 'evento_actual'));
    }

    // Método extra para la redirección de registro inicial que mencionamos antes
    public function createStepTwo()
    {
        // Retorna la vista del formulario extendido
        return view('participante.registro-especial'); 
    }
}
