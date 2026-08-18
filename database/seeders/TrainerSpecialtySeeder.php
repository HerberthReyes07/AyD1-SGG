<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrainerSpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            ['name' => 'Pérdida de peso', 'description' => 'Enfocado en reducir grasa corporal mediante cardio y planes de entrenamiento controlados en calorías.', 'is_active' => true],
            ['name' => 'Ganancia muscular', 'description' => 'Enfocado en entrenamiento de fuerza e hipertrofia para aumentar masa muscular.', 'is_active' => true],
            ['name' => 'Rehabilitación', 'description' => 'Enfocado en recuperación y reintroducción segura a la actividad física tras una lesión.', 'is_active' => true],
            ['name' => 'Acondicionamiento general', 'description' => 'Enfocado en salud general, resistencia y movimiento funcional para la vida diaria.', 'is_active' => true],
        ];

        DB::table('trainer_specialties')->upsert($specialties, ['name'], ['description', 'is_active']);
    }
}
