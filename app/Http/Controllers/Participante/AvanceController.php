<?php

namespace App\Http\Controllers\Participante;

use App\Http\Controllers\Controller;
use App\Models\Avance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvanceController extends Controller
{
    /**
     * Muestra el historial de avances y el formulario.
     */
    public function index()
    {
        $participante = Auth::user()->participante;
        $equipo = $participante->equipos->first();

        // Validación de seguridad básica
        if (!$equipo || !$equipo->proyecto) {
            return redirect()->route('participante.dashboard')
                ->with('error', 'Debes tener un equipo y proyecto registrado para subir avances.');
        }

        $proyecto = $equipo->proyecto;
        // Cargamos los avances ordenados cronológicamente
        $avances = $proyecto->avances()->orderBy('created_at', 'desc')->get();

        return view('participante.avances.index', compact('proyecto', 'avances'));
    }

    /**
     * Guarda un nuevo registro en la bitácora.
     */
    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|min:10',
        ]);

        $equipo = Auth::user()->participante->equipos->first();
        $proyecto = $equipo->proyecto;

        Avance::create([
            'proyecto_id' => $proyecto->id,
            'descripcion' => $request->descripcion,
            'fecha'       => now(), // O $request->fecha si quieres permitir fechas pasadas
        ]);

        return back()->with('success', 'Avance registrado en la bitácora correctamente.');
    }
    
    // Opcional: Eliminar avance (solo si lo escribió él o es líder)
    public function destroy($id)
    {
        $avance = Avance::findOrFail($id);
        // Aquí podrías agregar validación de propiedad
        $avance->delete();
        return back()->with('success', 'Registro eliminado.');
    }
}
