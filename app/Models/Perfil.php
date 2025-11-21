<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perfil extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'perfiles';

    protected $fillable = ['nombre'];

    public function participantes()
    {
        return $this->belongsToMany(Participante::class, 'equipo_participante');
    }
}
