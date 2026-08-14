<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use App\Models\MemberMembership;
use App\Services\MembershipService;
use App\Repositories\MembershipRepository;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Exception;

class MembershipController extends Controller
{
    protected MembershipService $membershipService;
    protected MembershipRepository $membershipRepository;
    protected MemberService $memberService;

    public function __construct(
        MembershipService $membershipService,
        MembershipRepository $membershipRepository,
        MemberService $memberService
    ) {
        $this->membershipService = $membershipService;
        $this->membershipRepository = $membershipRepository;
        $this->memberService = $memberService;
    }

    /**
     * Display a listing of memberships.
     */
    public function index(Request $request)
    {
        $memberships = MemberMembership::with(['member.user', 'plan', 'payments'])
            ->orderByDesc('id')
            ->get();

        $search = $request->input('search');
        $members = $this->memberService->getAllMembersFiltering($search);

        return view('memberships.control.index', compact('memberships', 'members', 'search'));
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

        return view('memberships.control.create', compact('plans', 'members'));
    }

    /**
     * Display the specified membership.
     */
    public function show(string|int $id, int|string $memberId)
    {
        $membership = $this->membershipRepository->findById($id);

        $member = $this->memberService->getMemberById($memberId);
        if (!$membership) {
            abort(404, 'Membresía no encontrada.');
        }

        return view('memberships.control.show', compact('membership', 'member'));
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


    /**
     * Get member memberships
     */
    public function memberMemberships(int|string $memberId)
    {
        $member = $this->memberService->getMemberById($memberId);

        if (!$member || !$member->member) {
            abort(404, 'Socio no encontrado.');
        }

        $memberships = $this->membershipService->getMemberMemberships($memberId);

        return view('memberships.control.member-memberships', compact(
            'member',
            'memberships'
        ));
    }
}
