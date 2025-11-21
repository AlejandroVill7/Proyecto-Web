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
}
