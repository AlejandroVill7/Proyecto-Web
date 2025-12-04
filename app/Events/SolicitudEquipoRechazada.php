<?php

namespace App\Events;

use App\Models\SolicitudEquipo;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SolicitudEquipoRechazada
{
    use Dispatchable, SerializesModels;

    public $solicitud;

    public function __construct(SolicitudEquipo $solicitud)
    {
        $this->solicitud = $solicitud;
    }
}

