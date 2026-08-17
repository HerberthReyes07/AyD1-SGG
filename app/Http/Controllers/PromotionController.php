<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Enums\PromotionType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class PromotionController extends Controller
{
    /**
     * Display a listing of promotions.
     */
    public function index()
    {
        $promotions = Promotion::with('authorizedBy')->orderByDesc('id')->get();

        return view('promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new promotion.
     */
    public function create()
    {
        return view('promotions.create');
    }

    /**
     * Store a newly created promotion in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => ['required', new Enum(PromotionType::class)],
            'value'      => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('type') === PromotionType::Percentage->value && $value > 100) {
                        $fail('El porcentaje no puede ser mayor al 100%.');
                    }
                },
            ],
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $validated['is_active']     = true;
        $validated['authorized_by'] = $request->user()->id;

        Promotion::create($validated);

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Promoción creada y autorizada correctamente.');
    }

    /**
     * Show the form for editing the specified promotion.
     */
    public function edit(Promotion $promotion)
    {
        return view('promotions.edit', compact('promotion'));
    }

    /**
     * Update the specified promotion in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => ['required', new Enum(PromotionType::class)],
            'value'      => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('type') === PromotionType::Percentage->value && $value > 100) {
                        $fail('El porcentaje no puede ser mayor al 100%.');
                    }
                },
            ],
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $promotion->update($validated);

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Promoción actualizada correctamente.');
    }

    /**
     * Toggle active status of promotion.
     */
    public function toggleStatus(Promotion $promotion)
    {
        $promotion->update([
            'is_active' => !$promotion->is_active,
        ]);

        $statusText = $promotion->is_active ? 'activada' : 'desactivada';

        return redirect()
            ->route('promotions.index')
            ->with('success', "Promoción '{$promotion->name}' {$statusText} correctamente.");
    }
}
