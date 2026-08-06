<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Cardio', 'description' => 'Clases de ejercicios cardiovasculares para mejorar la resistencia y la salud del corazón.'],
            ['name' => 'Fuerza', 'description' => 'Clases enfocadas en el desarrollo de la fuerza muscular y tonificación.'],
            ['name' => 'Flexibilidad', 'description' => 'Clases que mejoran la flexibilidad y movilidad del cuerpo.'],
            ['name' => 'Pilates', 'description' => 'Clases de Pilates para fortalecer el core y mejorar la postura.'],
            ['name' => 'Yoga', 'description' => 'Clases de Yoga para mejorar la flexibilidad, fuerza y bienestar mental.'],
        ];

        DB::table('class_categories')->upsert($categories, ['name'], ['description']);
    }
}
