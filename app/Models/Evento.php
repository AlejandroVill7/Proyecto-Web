<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'eventos';

    protected $fillable = ['nombre', 'descripcion', 'fecha_inicio', 'fecha_fin'];

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }

    public function criterios()
    {
        return $this->hasMany(CriterioEvaluacion::class);
    }

    public function constancias()
    {
        return $this->hasMany(Constancia::class);
    }
}
