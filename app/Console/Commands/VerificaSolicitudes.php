<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SolicitudEquipo;
use App\Models\Equipo;

class VerificaSolicitudes extends Command
{
    protected $signature = 'solicitudes:verificar {--equipo=11}';
    protected $description = 'Verifica las solicitudes en la base de datos';

    public function handle()
    {
        $this->info('=== RESUMEN DE SOLICITUDES ===\n');
        
        // Mostrar todas las solicitudes
        $todas = SolicitudEquipo::with(['equipo', 'participante.user'])->orderBy('equipo_id')->get();
        
        $this->info('TODAS LAS SOLICITUDES:');
        foreach ($todas as $s) {
            $badge = match($s->estado) {
                'pendiente' => '[PENDIENTE]',
                'aceptada' => '[ACEPTADA]',
                'rechazada' => '[RECHAZADA]',
                default => '[UNKNOWN]'
            };
            $this->line("{$badge} Equipo {$s->equipo_id} ({$s->equipo->nombre}): {$s->participante->user->name}");
        }
        
        // Mostrar qué ve cada líder
        $this->info("\n=== QUÉ VE CADA LÍDER EN SU DASHBOARD ===\n");
        
        $equipos = \App\Models\Equipo::with(['solicitudesPendientes.participante.user'])->get();
        
        foreach ($equipos as $e) {
            $lider = $e->getLider();
            if (!$lider) continue;
            
            $pendientes = $e->solicitudesPendientes()->count();
            if ($pendientes > 0) {
                $this->line("📋 {$lider->user->name} (Líder de {$e->nombre}):");
                $this->line("   Solicitudes pendientes: {$pendientes}");
                foreach ($e->solicitudesPendientes as $s) {
                    $this->line("   • {$s->participante->user->name}");
                }
            }
        }
        
        $this->info("\n=== ESTADÍSTICAS ===");
        $this->line("Total: " . $todas->count());
        $this->line("Pendientes: " . $todas->where('estado', 'pendiente')->count());
        $this->line("Aceptadas: " . $todas->where('estado', 'aceptada')->count());
        $this->line("Rechazadas: " . $todas->where('estado', 'rechazada')->count());
    }
}
