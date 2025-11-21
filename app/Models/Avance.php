<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Avance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'avances';

    protected $fillable = ['proyecto_id', 'descripcion', 'fecha'];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }
}
