<?php

namespace App\Http\Controllers;

use App\Repositories\MembershipRepository;
use App\Services\MembershipService;
use Illuminate\Http\Request;
use Exception;

class MemberMembershipController extends Controller
{
    protected MembershipService    $membershipService;
    protected MembershipRepository $membershipRepository;

    public function __construct(
        MembershipService    $membershipService,
        MembershipRepository $membershipRepository,
    ) {
        $this->membershipService    = $membershipService;
        $this->membershipRepository = $membershipRepository;
    }

    /**
     * Display a listing of the member's memberships.
     *
     * GET /my-memberships
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $member = $user->member;
        abort_if(!$member, 403);

        $memberships = $this->membershipRepository->findByMemberId($member->user_id);

        return view('memberships.member.index', compact('memberships'));
    }

    /**
     * Display details of a specific membership.
     *
     * GET /my-memberships/{id}
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $member = $user->member;
        abort_if(!$member, 403);

        $membership = $this->membershipRepository->findById($id);

        if (!$membership || (string) $membership->member_id !== (string) $member->user_id) {
            abort(404, 'Membresía no encontrada.');
        }

        return view('memberships.member.show', compact('membership', 'member'));
    }

    /**
     * Cancel the member's own membership.
     *
     * POST /my-memberships/{id}/cancel
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $user = $request->user();
        $member = $user->member;
        abort_if(!$member, 403);

        $membership = $this->membershipRepository->findById($id);

        if (!$membership || (string) $membership->member_id !== (string) $member->user_id) {
            abort(404, 'Membresía no encontrada.');
        }

        try {
            $this->membershipService->cancelMembership($id, $request->reason, $user->id);
            return redirect()
                ->route('member-memberships.show', $id)
                ->with('success', 'Tu membresía ha sido cancelada correctamente.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Freeze the authenticated member's active membership.
     *
     * POST /my-membership/freeze
     */
    public function freeze(Request $request)
    {
        $request->validate([
            'reason'                      => 'required|string|max:255',
            'estimated_reactivation_date' => [
                'nullable',
                'date',
                'after:today',
                'before_or_equal:' . now()->addDays(15)->toDateString(),
            ],
        ]);

        $user = $request->user();

        // Resolve the member record linked to the authenticated user.
        $member = $user->member;
        abort_if(!$member, 403);

        // Find the current active membership for this member.
        $membership = $this->membershipRepository->findCurrentByMemberId($member->user_id);

        if (!$membership) {
            return back()->withErrors(['error' => 'No tienes una membresía activa para congelar.']);
        }

        try {
            $this->membershipService->freezeMembership(
                membershipId:               $membership->id,
                requestingMemberId:         $member->user_id,
                reason:                     $request->reason,
                estimatedReactivationDate:  $request->estimated_reactivation_date,
            );

            return back()->with('success', 'Tu membresía ha sido congelada correctamente.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
