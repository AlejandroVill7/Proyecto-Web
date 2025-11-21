<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calificacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'calificaciones';

    protected $fillable = ['proyecto_id', 'juez_user_id', 'criterio_id', 'puntuacion'];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function juez()
    {
        return $this->belongsTo(User::class, 'juez_user_id');
    }

    public function criterio()
    {
        return $this->belongsTo(CriterioEvaluacion::class, 'criterio_id');
    }
}
