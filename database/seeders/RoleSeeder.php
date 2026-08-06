<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Tiene control total del sistema. Administra empleados, planes de membresía, clases grupales, promociones y el catálogo de alimentos, y puede ver todos los socios y reportes.'],
            ['name' => 'receptionist', 'description' => 'Atiende el servicio de recepción: registro de entrada/salida, pagos de membresía, inscripción presencial a clases y registro de pases para invitados.'],
            ['name' => 'trainer', 'description' => 'Imparte las clases grupales asignadas y realiza seguimiento personalizado a los socios asignados, incluyendo rutinas, mediciones de progreso y observaciones nutricionales.'],
            ['name' => 'member', 'description' => 'Cliente del gimnasio que puede gestionar su membresía, inscribirse en clases grupales, registrar su progreso físico y registrar comidas mediante el módulo de nutrición.'],
        ];

        DB::table('roles')->upsert($roles, ['name'], ['description']);
    }
}
