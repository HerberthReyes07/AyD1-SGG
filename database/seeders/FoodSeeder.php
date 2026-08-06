<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Food;
use App\Models\FoodCategory;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cargar categorías existentes (FoodCategorySeeder)
        $categories = FoodCategory::whereIn('name', [
            'Proteínas', 'Granos', 'Frutas', 'Verduras', 'Lácteos', 'Grasas saludables'
        ])->get()->keyBy('name');

        $foods = [
            [
                'name' => 'Pechuga de pollo',
                'category_id' => $categories['Proteínas']->id ?? null,
                'calories_per_serving' => 165.00,
                'protein_g' => 31.00,
                'carbs_g' => 0.00,
                'fat_g' => 3.60,
                'reference_serving_g' => 100.00,
                'is_active' => true,
            ],
            [
                'name' => 'Arroz blanco cocido',
                'category_id' => $categories['Granos']->id ?? null,
                'calories_per_serving' => 130.00,
                'protein_g' => 2.70,
                'carbs_g' => 28.00,
                'fat_g' => 0.30,
                'reference_serving_g' => 100.00,
                'is_active' => true,
            ],
            [
                'name' => 'Manzana (cruda)',
                'category_id' => $categories['Frutas']->id ?? null,
                'calories_per_serving' => 52.00,
                'protein_g' => 0.26,
                'carbs_g' => 14.00,
                'fat_g' => 0.17,
                'reference_serving_g' => 100.00,
                'is_active' => true,
            ],
            [
                'name' => 'Brócoli (crudo)',
                'category_id' => $categories['Verduras']->id ?? null,
                'calories_per_serving' => 34.00,
                'protein_g' => 2.82,
                'carbs_g' => 6.64,
                'fat_g' => 0.37,
                'reference_serving_g' => 100.00,
                'is_active' => true,
            ],
            [
                'name' => 'Aguacate',
                'category_id' => $categories['Grasas saludables']->id ?? null,
                'calories_per_serving' => 160.00,
                'protein_g' => 2.00,
                'carbs_g' => 9.00,
                'fat_g' => 15.00,
                'reference_serving_g' => 100.00,
                'is_active' => true,
            ],
            [
                'name' => 'Yogur natural',
                'category_id' => $categories['Lácteos']->id ?? null,
                'calories_per_serving' => 59.00,
                'protein_g' => 10.00,
                'carbs_g' => 3.60,
                'fat_g' => 0.40,
                'reference_serving_g' => 100.00,
                'is_active' => true,
            ],
        ];

        // Filtrar entradas cuya categoría no exista en la BD (evita upsert con category_id nulo)
        $foods = array_filter($foods, function ($f) {
            return !is_null($f['category_id']);
        });

        DB::table('foods')->upsert(
            array_values($foods),
            ['name'],
            ['category_id', 'calories_per_serving', 'protein_g', 'carbs_g', 'fat_g', 'reference_serving_g', 'is_active']
        );
    }
}
