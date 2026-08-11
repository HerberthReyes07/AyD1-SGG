<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\MembershipPlan;
use App\Models\Promotion;
use App\Services\PaymentService;
use App\Repositories\PaymentRepository;
use Illuminate\Http\Request;
use Exception;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;
    protected PaymentRepository $paymentRepository;

    public function __construct(
        PaymentService $paymentService,
        PaymentRepository $paymentRepository
    ) {
        $this->paymentService = $paymentService;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Display a listing of payments.
     */
    public function index()
    {
        $payments = $this->paymentRepository->getAll();
        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(Request $request)
    {
        $plans = MembershipPlan::all();
        $paymentMethods = PaymentMethod::all();
        $promotions = Promotion::where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();
            
        $members = \App\Models\User::whereHas('role', function ($q) {
            $q->where('name', 'member');
        })->with('member')->get();

        $selectedMemberId = $request->query('member_id');
        $isRenewal = $request->query('renewal', false);

        return view('payments.create', compact('plans', 'paymentMethods', 'promotions', 'members', 'selectedMemberId', 'isRenewal'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,user_id',
            'plan_id' => 'required|exists:membership_plans,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'promotion_id' => 'nullable|exists:promotions,id',
            'is_renewal' => 'nullable|boolean',
        ]);

        $validated['registered_by'] = $request->user()->id;

        try {
            if ($request->boolean('is_renewal')) {
                $payment = $this->paymentService->registerRenewalPayment($validated);
            } else {
                $payment = $this->paymentService->registerPayment($validated);
            }

            return redirect()
                ->route('payments.index')
                ->with('success', 'Pago registrado y membresía procesada correctamente.');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified payment.
     */
    public function show(string|int $id)
    {
        $payment = $this->paymentRepository->findById($id);
        if (!$payment) {
            abort(404, 'Pago no encontrado.');
        }

        return view('payments.show', compact('payment'));
    }
}

