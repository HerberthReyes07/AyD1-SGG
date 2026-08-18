<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FoodController extends Controller
{
    public function index(Request $request)
    {
        $query = Food::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $foods = $query
            ->orderBy('name')
            ->get();

        $categories = FoodCategory::orderBy('name')->get();

        return view('foods.index', compact('foods', 'categories'));
    }

    public function create()
    {
        $categories = FoodCategory::orderBy('name')->get();

        return view('foods.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('foods', 'name'),
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:food_categories,id',
            ],
            'calories_per_serving' => [
                'required',
                'numeric',
                'min:0',
            ],
            'protein_g' => [
                'required',
                'numeric',
                'min:0',
            ],
            'carbs_g' => [
                'required',
                'numeric',
                'min:0',
            ],
            'fat_g' => [
                'required',
                'numeric',
                'min:0',
            ],
            'reference_serving_g' => [
                'required',
                'numeric',
                'gt:0',
            ],
        ]);

        $validated['is_active'] = true;

        Food::create($validated);

        return redirect()
            ->route('foods.index')
            ->with('success', 'Alimento registrado correctamente.');
    }

    public function edit(Food $food)
{
    $categories = FoodCategory::orderBy('name')->get();

    return view('foods.edit', compact('food', 'categories'));
}

public function update(Request $request, Food $food)
{
    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:150',
            Rule::unique('foods', 'name')->ignore($food->id),
        ],
        'category_id' => [
            'required',
            'integer',
            'exists:food_categories,id',
        ],
        'calories_per_serving' => [
            'required',
            'numeric',
            'min:0',
        ],
        'protein_g' => [
            'required',
            'numeric',
            'min:0',
        ],
        'carbs_g' => [
            'required',
            'numeric',
            'min:0',
        ],
        'fat_g' => [
            'required',
            'numeric',
            'min:0',
        ],
        'reference_serving_g' => [
            'required',
            'numeric',
            'gt:0',
        ],
    ]);

    $food->update($validated);

    return redirect()
        ->route('foods.index')
        ->with('success', 'Alimento actualizado correctamente.');
}

public function toggleStatus(Food $food)
{
    $food->update([
        'is_active' => ! $food->is_active,
    ]);

    $message = $food->is_active
        ? 'Alimento activado correctamente.'
        : 'Alimento desactivado correctamente.';

    return redirect()
        ->route('foods.index')
        ->with('success', $message);
}
}