<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use App\Models\MemberMembership;
use App\Services\MembershipService;
use App\Repositories\MembershipRepository;
use Illuminate\Http\Request;
use Exception;

class MembershipController extends Controller
{
    protected MembershipService $membershipService;
    protected MembershipRepository $membershipRepository;

    public function __construct(
        MembershipService $membershipService,
        MembershipRepository $membershipRepository
    ) {
        $this->membershipService = $membershipService;
        $this->membershipRepository = $membershipRepository;
    }

    /**
     * Display a listing of memberships.
     */
    public function index(Request $request)
    {
        $memberships = MemberMembership::with(['member.user', 'plan', 'payments'])
            ->orderByDesc('id')
            ->get();

        return view('memberships.index', compact('memberships'));
    }

    /**
     * Show available membership plans and the purchase form.
     */
    public function create()
    {
        $plans = MembershipPlan::all();
        $members = \App\Models\User::whereHas('role', function ($q) {
            $q->where('name', 'member');
        })->with('member')->get();

        return view('memberships.create', compact('plans', 'members'));
    }

    /**
     * Display the specified membership.
     */
    public function show(string|int $id)
    {
        $membership = $this->membershipRepository->findById($id);
        if (!$membership) {
            abort(404, 'Membresía no encontrada.');
        }

        return view('memberships.show', compact('membership'));
    }

    /**
     * Cancel a membership (DELETE request).
     */
    public function destroy(Request $request, string|int $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        try {
            $this->membershipService->cancelMembership($id, $request->reason, $request->user()->id);
            return redirect()
                ->route('memberships.show', $id)
                ->with('success', 'Membresía cancelada correctamente.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

