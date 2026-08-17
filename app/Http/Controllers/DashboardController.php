<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
    ) {}

    /**
     * Muestra el dashboard, cargando las relaciones necesarias para
     * poder personalizar el saludo y las tarjetas según el rol.
     */
    public function index(Request $request): View
    {
        $user = $request->user()->load(['role', 'trainer.specialty', 'member']);

        $stats = match ($user->role?->name) {
            'admin' => $this->dashboardService->getAdminStats(),
            'receptionist' => $this->dashboardService->getReceptionistStats(),
            'trainer' => $user->trainer ? $this->dashboardService->getTrainerStats($user->trainer) : [],
            'member' => $user->member ? $this->dashboardService->getMemberStats($user->member) : [],
            default => [],
        };

        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }
}
