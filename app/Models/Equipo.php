<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipos';

    protected $fillable = ['nombre'];

    public function proyecto()
    {
        return $this->hasOne(Proyecto::class);
    }

    public function participantes()
    {
        return $this->belongsToMany(Participante::class, 'equipo_participante')->withPivot('perfil_id');
    }
    public function removerIntegrante($participanteId)
    {
        $idPerfilLider = 3;

        // 1. Verificar si el que se va TIENE EL PERFIL DE LÍDER
        $esLider = $this->participantes()
            ->wherePivot('participante_id', $participanteId)
            ->wherePivot('perfil_id', $idPerfilLider)
            ->exists();

        // 2. Eliminar al usuario
        $this->participantes()->detach($participanteId);

        // 3. Lógica de Sucesión
        if ($esLider) {
            // Buscamos al miembro más antiguo que queda
            $nuevoLider = $this->participantes()
                ->orderBy('equipo_participante.created_at', 'asc')
                ->first();

            if ($nuevoLider) {
                // Le asignamos el PERFIL DE LÍDER (ID 3)
                $this->participantes()->updateExistingPivot($nuevoLider->id, ['perfil_id' => $idPerfilLider]);
            }
        }
    }
}
