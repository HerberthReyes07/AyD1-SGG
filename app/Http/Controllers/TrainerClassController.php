<?php

namespace App\Http\Controllers;

use App\Enums\ClassEnrollmentStatus;
use App\Enums\ClassSessionStatus;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TrainerClassController extends Controller
{
    public function index(Request $request)
    {
        $trainer = $request->user()->trainer;

        abort_if(! $trainer, 403);

        $sessions = ClassSession::with([
            'groupClass.category',
        ])
            ->withCount([
                'enrollments as participant_count' => function ($query) {
                    $query->whereIn('status', [
                        ClassEnrollmentStatus::Enrolled->value,
                        ClassEnrollmentStatus::Attended->value,
                        ClassEnrollmentStatus::NoShow->value,
                    ]);
                },
            ])
            ->whereHas('groupClass', function ($query) use ($trainer) {
                $query->where(
                    'trainer_id',
                    $trainer->user_id
                );
            })
            ->orderByDesc('starts_at')
            ->get();

        return view(
            'trainer-classes.index',
            compact('sessions')
        );
    }

    public function show(
        Request $request,
        ClassSession $session
    ) {
        $this->ensureTrainerOwnsSession(
            $request,
            $session
        );

        $session->load([
            'groupClass.category',
        ]);

        $enrollments = $session->enrollments()
            ->with('member.user')
            ->whereIn('status', [
                ClassEnrollmentStatus::Enrolled->value,
                ClassEnrollmentStatus::Attended->value,
                ClassEnrollmentStatus::NoShow->value,
            ])
            ->orderBy('enrollment_date')
            ->get();

        return view(
            'trainer-classes.show',
            compact(
                'session',
                'enrollments'
            )
        );
    }

    public function start(
        Request $request,
        ClassSession $session
    ) {
        $this->ensureTrainerOwnsSession(
            $request,
            $session
        );

        if (! in_array($session->status, [
            ClassSessionStatus::Scheduled,
            ClassSessionStatus::Rescheduled,
        ], true)) {
            throw ValidationException::withMessages([
                'session' => 'Esta sesion no puede ser iniciada.',
            ]);
        }

        $session->update([
            'status' => ClassSessionStatus::InProgress,
        ]);

        return redirect()
            ->route(
                'trainer-classes.show',
                $session
            )
            ->with(
                'success',
                'Sesion iniciada correctamente.'
            );
    }

    public function updateAttendance(
        Request $request,
        ClassSession $session
    ) {
        $this->ensureTrainerOwnsSession(
            $request,
            $session
        );

        if (
            $session->status !==
            ClassSessionStatus::InProgress
        ) {
            throw ValidationException::withMessages([
                'session' => 'La asistencia solo puede registrarse mientras la sesion esta en progreso.',
            ]);
        }

        $validated = $request->validate([
            'attendance' => [
                'required',
                'array',
            ],
            'attendance.*' => [
                'required',
                Rule::in([
                    ClassEnrollmentStatus::Attended->value,
                    ClassEnrollmentStatus::NoShow->value,
                ]),
            ],
        ]);

        DB::transaction(function () use (
            $session,
            $validated
        ) {
            foreach (
                $validated['attendance']
                as $enrollmentId => $status
            ) {
                $enrollment = $session
                    ->enrollments()
                    ->where(
                        'id',
                        $enrollmentId
                    )
                    ->whereIn('status', [
                        ClassEnrollmentStatus::Enrolled->value,
                        ClassEnrollmentStatus::Attended->value,
                        ClassEnrollmentStatus::NoShow->value,
                    ])
                    ->first();

                if (! $enrollment) {
                    throw ValidationException::withMessages([
                        'attendance' => 'Se encontro una inscripcion invalida para esta sesion.',
                    ]);
                }

                $enrollment->update([
                    'status' => $status,
                ]);
            }
        });

        return redirect()
            ->route(
                'trainer-classes.show',
                $session
            )
            ->with(
                'success',
                'Asistencia actualizada correctamente.'
            );
    }

    public function complete(
        Request $request,
        ClassSession $session
    ) {
        $this->ensureTrainerOwnsSession(
            $request,
            $session
        );

        if (
            $session->status !==
            ClassSessionStatus::InProgress
        ) {
            throw ValidationException::withMessages([
                'session' => 'Solo una sesion en progreso puede finalizarse.',
            ]);
        }

        DB::transaction(function () use ($session) {

            $session->enrollments()
                ->where(
                    'status',
                    ClassEnrollmentStatus::Enrolled->value
                )
                ->update([
                    'status' =>
                        ClassEnrollmentStatus::NoShow->value,
                ]);

            $session->update([
                'status' =>
                    ClassSessionStatus::Completed,
            ]);
        });

        return redirect()
            ->route(
                'trainer-classes.show',
                $session
            )
            ->with(
                'success',
                'Sesion finalizada correctamente.'
            );
    }

    private function ensureTrainerOwnsSession(
        Request $request,
        ClassSession $session
    ): void {
        $trainer = $request->user()->trainer;

        abort_if(! $trainer, 403);

        $session->loadMissing('groupClass');

        abort_if(
            $session->groupClass->trainer_id !==
            $trainer->user_id,
            403
        );
    }
}