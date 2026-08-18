<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Frutas', 'description' => 'Alimentos de origen vegetal que son dulces y jugosos, como manzanas, naranjas, plátanos y fresas.'],
            ['name' => 'Verduras', 'description' => 'Alimentos de origen vegetal que son nutritivos y bajos en calorías, como espinacas, zanahorias, brócoli y pimientos.'],
            ['name' => 'Proteínas', 'description' => 'Alimentos ricos en proteínas que ayudan a construir y reparar tejidos, como carnes magras, pescado, huevos y legumbres.'],
            ['name' => 'Granos', 'description' => 'Alimentos que provienen de cereales y granos enteros, como arroz integral, avena, quinoa y pan integral.'],
            ['name' => 'Lácteos', 'description' => 'Alimentos derivados de la leche que son ricos en calcio y proteínas, como leche, yogur y queso.'],
            ['name' => 'Grasas saludables', 'description' => 'Alimentos que contienen grasas saludables para el corazón, como aguacates, nueces, semillas y aceite de oliva.'],
        ];

        DB::table('food_categories')->upsert($categories, ['name'], ['description']);
    }
}
