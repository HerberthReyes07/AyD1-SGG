<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class MembershipPlanController extends Controller
{
    /**
     * Display a listing of membership plans.
     */
    public function index()
    {
        $plans = MembershipPlan::orderBy('id')->get();

        return view('membership-plans.index', compact('plans'));
    }

    /**
     * Show the form for editing the specified membership plan.
     */
    public function edit(MembershipPlan $membershipPlan)
    {
        return view('membership-plans.edit', ['plan' => $membershipPlan]);
    }

    /**
     * Update the specified membership plan in storage.
     * EXCLUSIVELY updates price and description.
     */
    public function update(Request $request, MembershipPlan $membershipPlan)
    {
        $validated = $request->validate([
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $membershipPlan->update([
            'price'       => $validated['price'],
            'description' => $validated['description'],
        ]);

        return redirect()
            ->route('membership-plans.index')
            ->with('success', "Plan '{$membershipPlan->name}' actualizado correctamente.");
    }
}
