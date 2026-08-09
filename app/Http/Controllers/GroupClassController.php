<?php

namespace App\Http\Controllers;

use App\Models\ClassCategory;
use App\Models\GroupClass;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GroupClassController extends Controller
{
    public function index(Request $request)
    {
        $query = GroupClass::with([
            'category',
            'trainer.user',
        ]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where(
                'is_active',
                $request->status === 'active'
            );
        }

        $groupClasses = $query
            ->orderBy('name')
            ->get();

        $categories = ClassCategory::orderBy('name')->get();

        return view('group-classes.index', compact(
            'groupClasses',
            'categories'
        ));
    }

    public function create()
    {
        $categories = ClassCategory::orderBy('name')->get();

        $trainers = Trainer::with([
            'user',
            'specialty',
        ])
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })
            ->get();

        return view('group-classes.create', compact(
            'categories',
            'trainers'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('group_classes', 'name'),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'duration_minutes' => [
                'required',
                'integer',
                'min:1',
            ],

            'max_participants' => [
                'required',
                'integer',
                'min:1',
            ],

            'category_id' => [
                'nullable',
                'integer',
                'exists:class_categories,id',
            ],

            'trainer_id' => [
                'nullable',
                'integer',
                'exists:trainers,user_id',
            ],
        ]);

        $validated['is_active'] = true;

        GroupClass::create($validated);

        return redirect()
            ->route('group-classes.index')
            ->with(
                'success',
                'Plantilla de clase grupal registrada correctamente.'
            );
    }

    public function edit(GroupClass $groupClass)
    {
        $categories = ClassCategory::orderBy('name')->get();

        $trainers = Trainer::with([
            'user',
            'specialty',
        ])
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })
            ->get();

        return view('group-classes.edit', compact(
            'groupClass',
            'categories',
            'trainers'
        ));
    }

    public function update(Request $request, GroupClass $groupClass)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('group_classes', 'name')
                    ->ignore($groupClass->id),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'duration_minutes' => [
                'required',
                'integer',
                'min:1',
            ],

            'max_participants' => [
                'required',
                'integer',
                'min:1',
            ],

            'category_id' => [
                'nullable',
                'integer',
                'exists:class_categories,id',
            ],

            'trainer_id' => [
                'nullable',
                'integer',
                'exists:trainers,user_id',
            ],
        ]);

        $groupClass->update($validated);

        return redirect()
            ->route('group-classes.index')
            ->with(
                'success',
                'Plantilla de clase grupal actualizada correctamente.'
            );
    }

    public function toggleStatus(GroupClass $groupClass)
    {
        $groupClass->update([
            'is_active' => ! $groupClass->is_active,
        ]);

        $message = $groupClass->is_active
            ? 'Clase grupal activada correctamente.'
            : 'Clase grupal desactivada correctamente.';

        return redirect()
            ->route('group-classes.index')
            ->with('success', $message);
    }
}