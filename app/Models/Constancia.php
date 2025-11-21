<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Constancia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'constancias';

    protected $fillable = ['participante_id', 'evento_id', 'tipo', 'archivo_path', 'codigo_qr'];

    public function participante()
    {
        return $this->belongsTo(Participante::class);
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}
