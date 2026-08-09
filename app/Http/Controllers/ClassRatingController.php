<?php

namespace App\Http\Controllers;

use App\Enums\ClassEnrollmentStatus;
use App\Enums\ClassSessionStatus;
use App\Models\ClassEnrollment;
use App\Models\ClassRating;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClassRatingController extends Controller
{
    public function store(
        Request $request,
        ClassEnrollment $enrollment
    ) {
        $member = $request->user()->member;

        abort_if(! $member, 403);

        abort_if(
            $enrollment->member_id !== $member->user_id,
            403
        );

        $enrollment->load([
            'classSession',
            'classRating',
        ]);

        if (
            $enrollment->status !==
            ClassEnrollmentStatus::Attended
        ) {
            throw ValidationException::withMessages([
                'rating' => 'Solo puedes calificar clases a las que asististe.',
            ]);
        }

        if (
            $enrollment->classSession->status !==
            ClassSessionStatus::Completed
        ) {
            throw ValidationException::withMessages([
                'rating' => 'La clase debe estar completada antes de poder calificarla.',
            ]);
        }

        if ($enrollment->classRating) {
            throw ValidationException::withMessages([
                'rating' => 'Esta clase ya fue calificada.',
            ]);
        }

        $validated = $request->validate([
            'rating' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'comment' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        ClassRating::create([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'class_enrollment_id' => $enrollment->id,
        ]);

        return redirect()
            ->route('member-classes.history')
            ->with(
                'success',
                'Calificacion registrada correctamente.'
            );
    }
}