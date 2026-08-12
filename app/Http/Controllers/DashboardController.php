<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard, cargando las relaciones necesarias para
     * poder personalizar el saludo y las tarjetas según el rol.
     */
    public function index(Request $request): View
    {
        $user = $request->user()->load(['role', 'trainer.specialty', 'member']);

        return view('dashboard', [
            'user' => $user,
        ]);
    }
}
