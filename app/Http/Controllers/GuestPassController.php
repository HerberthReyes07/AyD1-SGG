<?php

namespace App\Http\Controllers;

use App\Models\GuestPass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuestPassController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date_from' => [
                'nullable',
                'date',
            ],
            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
        ]);

        $query = GuestPass::with('registeredBy');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', '%' . $search . '%')
                    ->orWhere('dpi', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('visit_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('visit_date', '<=', $request->date_to);
        }

        $totalGuestPasses = (clone $query)->count();

        $guestPasses = $query
            ->orderByDesc('visit_date')
            ->orderByDesc('id')
            ->get();

        return view('guest-passes.index', compact(
            'guestPasses',
            'totalGuestPasses'
        ));
    }

    public function create()
    {
        return view('guest-passes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => [
                'required',
                'string',
                'max:255',
            ],
            'dpi' => [
                'required',
                'digits:13',
                Rule::unique('guest_passes', 'dpi'),
            ],
            'visit_date' => [
                'required',
                'date',
            ],
        ]);

        $validated['registered_by'] = $request->user()->id;

        GuestPass::create($validated);

        return redirect()
            ->route('guest-passes.index')
            ->with('success', 'Pase de invitado registrado correctamente.');
    }
}