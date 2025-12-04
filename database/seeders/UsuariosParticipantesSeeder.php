<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rol;
use App\Models\Participante;
use App\Models\Carrera;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosParticipantesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolParticipante = Rol::where('nombre', 'Participante')->first();
        
        if (!$rolParticipante) {
            $this->command->error('El rol "Participante" no existe. Ejecuta primero el seeder DatabaseSeeder.');
            return;
        }

        $carreras = Carrera::all();
        
        if ($carreras->isEmpty()) {
            $this->command->error('No hay carreras disponibles. Ejecuta primero el seeder DatabaseSeeder.');
            return;
        }

        $emails = [
            'lepsgapi@gmail.com',
            'alejandrozzzxx@gmail.com',
            'lesslyaragon@gmail.com',
            'carlosdiazvasquez0406@gmail.com',
        ];

        foreach ($emails as $email) {
            // Obtener o crear el usuario
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => explode('@', $email)[0],
                    'password' => Hash::make('password'),
                ]
            );

            // Asignar rol de Participante si no lo tiene
            if (!$user->roles()->where('nombre', 'Participante')->exists()) {
                $user->roles()->attach($rolParticipante->id);
            }

            // Crear participante si no existe
            if (!$user->participante) {
                Participante::create([
                    'user_id' => $user->id,
                    'carrera_id' => $carreras->random()->id,
                ]);
            }

            $this->command->line("Usuario procesado: {$email}");
        }

        $this->command->info('Usuarios participantes creados exitosamente.');
    }
}
