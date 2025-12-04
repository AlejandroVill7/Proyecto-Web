<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Participante;
use App\Models\Carrera;
use App\Models\Equipo;
use App\Models\Evento;

class UsuariosTestSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener evento activo o crear uno
        $evento = Evento::first() ?? Evento::create([
            'nombre' => 'Competencia 2025',
            'descripcion' => 'Competencia de programación',
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(30),
        ]);

        // Obtener carrera
        $carrera = Carrera::first();

        // 1. JUAN - Participante que se unirá a varios equipos
        $juan_user = User::create([
            'name' => 'Juan Participante',
            'email' => 'juan@test.com',
            'password' => bcrypt('password'),
        ]);
        $juan_participante = Participante::create([
            'user_id' => $juan_user->id,
            'carrera_id' => $carrera->id,
            'no_control' => '2025001',
        ]);

        // 2. TELLEZ JOEL - Líder del Equipo 1
        $tellez_user = User::create([
            'name' => 'Tellez Joel',
            'email' => 'tellez@test.com',
            'password' => bcrypt('password'),
        ]);
        $tellez_participante = Participante::create([
            'user_id' => $tellez_user->id,
            'carrera_id' => $carrera->id,
            'no_control' => '2025002',
        ]);

        // 3. PABLO LIDER - Líder del Equipo 2
        $pablo_user = User::create([
            'name' => 'Pablo Lider',
            'email' => 'pablo@test.com',
            'password' => bcrypt('password'),
        ]);
        $pablo_participante = Participante::create([
            'user_id' => $pablo_user->id,
            'carrera_id' => $carrera->id,
            'no_control' => '2025003',
        ]);

        // 4. CARLOS LIDER - Líder del Equipo 3
        $carlos_user = User::create([
            'name' => 'Carlos Lider',
            'email' => 'carlos@test.com',
            'password' => bcrypt('password'),
        ]);
        $carlos_participante = Participante::create([
            'user_id' => $carlos_user->id,
            'carrera_id' => $carrera->id,
            'no_control' => '2025004',
        ]);

        // Crear equipos
        $equipo_tellez = Equipo::create([
            'nombre' => 'Equipo Tellez',
        ]);

        $equipo_pablo = Equipo::create([
            'nombre' => 'Equipo Pablo',
        ]);

        $equipo_carlos = Equipo::create([
            'nombre' => 'Equipo Carlos',
        ]);

        // Agregar líderes a sus equipos (perfil_id = 3 es Líder)
        $equipo_tellez->participantes()->attach($tellez_participante->id, ['perfil_id' => 3]);
        $equipo_pablo->participantes()->attach($pablo_participante->id, ['perfil_id' => 3]);
        $equipo_carlos->participantes()->attach($carlos_participante->id, ['perfil_id' => 3]);

        echo "✅ Usuarios de prueba creados:\n";
        echo "Juan (Participante): juan@test.com / password\n";
        echo "Tellez Joel (Líder): tellez@test.com / password\n";
        echo "Pablo Lider (Líder): pablo@test.com / password\n";
        echo "Carlos Lider (Líder): carlos@test.com / password\n";
        echo "\n✅ Equipos creados:\n";
        echo "Equipo Tellez (ID: {$equipo_tellez->id})\n";
        echo "Equipo Pablo (ID: {$equipo_pablo->id})\n";
        echo "Equipo Carlos (ID: {$equipo_carlos->id})\n";
    }
}
