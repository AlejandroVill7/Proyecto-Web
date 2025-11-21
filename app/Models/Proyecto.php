<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proyecto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proyectos';

    protected $fillable = ['equipo_id', 'evento_id', 'nombre', 'descripcion', 'repositorio_url'];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function avances()
    {
        return $this->hasMany(Avance::class);
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }
}
