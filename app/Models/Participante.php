<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participante extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'participantes';

    protected $fillable = ['user_id', 'carrera_id', 'no_control'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'equipo_participante')->withPivot('perfil_id');
    }

    public function constancias()
    {
        return $this->hasMany(Constancia::class);
    }
}
