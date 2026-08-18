<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TrainerAssignment;
use App\Models\TrainerRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerRatingController extends Controller
{
    public function store(Request $request, TrainerAssignment $trainerAssignment)
    {
        abort_unless($trainerAssignment->member_id === Auth::user()->member->user_id, 403);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        TrainerRating::updateOrCreate(
            ['trainer_assignment_id' => $trainerAssignment->id],
            $validated
        );

        return back()->with('status', 'Calificación guardada correctamente.');
    }
}
