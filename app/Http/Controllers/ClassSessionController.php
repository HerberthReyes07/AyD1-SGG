<?php

namespace App\Http\Controllers;

use App\Enums\ClassSessionStatus;
use App\Models\ClassSession;
use App\Models\GroupClass;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClassSessionController extends Controller
{
    public function index(GroupClass $groupClass)
    {
        $groupClass->load([
            'category',
            'trainer.user',
        ]);

        $sessions = $groupClass->sessions()
            ->orderByDesc('starts_at')
            ->get();

        return view('class-sessions.index', compact(
            'groupClass',
            'sessions'
        ));
    }

    public function store(Request $request, GroupClass $groupClass)
    {
        if (! $groupClass->is_active) {
            return back()->withErrors([
                'starts_at' => 'No se pueden programar sesiones para una clase inactiva.',
            ]);
        }

        $validated = $request->validate([
            'starts_at' => [
                'required',
                'date',
                'after:now',
            ],
        ]);

        $startsAt = Carbon::parse($validated['starts_at'])
            ->format('Y-m-d H:i:s');

        $exists = ClassSession::where(
            'group_class_id',
            $groupClass->id
        )
            ->where('starts_at', $startsAt)
            ->where(
                'status',
                '!=',
                ClassSessionStatus::Cancelled->value
            )
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'starts_at' => 'Ya existe una sesion programada para esa fecha y hora.',
                ]);
        }

        ClassSession::create([
            'group_class_id' => $groupClass->id,
            'starts_at' => $startsAt,
            'status' => ClassSessionStatus::Scheduled,
            'change_reason' => null,
        ]);

        return redirect()
            ->route('class-sessions.index', $groupClass)
            ->with('success', 'Sesion programada correctamente.');
    }

    public function reschedule(
    Request $request,
    GroupClass $groupClass,
    ClassSession $session
    ) {
        $this->ensureSessionBelongsToGroupClass(
            $groupClass,
            $session
        );

        if (in_array($session->status, [
            ClassSessionStatus::Completed,
            ClassSessionStatus::Cancelled,
            ClassSessionStatus::InProgress,
        ], true)) {
            return back()->withErrors([
                'starts_at' => 'Esta sesion ya no puede ser reprogramada.',
            ]);
        }

        $validated = $request->validate([
            'starts_at' => [
                'required',
                'date',
                'after:now',
            ],
            'change_reason' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $startsAt = Carbon::parse($validated['starts_at'])
            ->format('Y-m-d H:i:s');

        $exists = ClassSession::where(
            'group_class_id',
            $groupClass->id
        )
            ->where('starts_at', $startsAt)
            ->where('id', '!=', $session->id)
            ->where(
                'status',
                '!=',
                ClassSessionStatus::Cancelled->value
            )
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'starts_at' => 'Ya existe otra sesion en esa fecha y hora.',
                ]);
        }

        $session->update([
            'starts_at' => $startsAt,
            'status' => ClassSessionStatus::Rescheduled,
            'change_reason' => $validated['change_reason'],
        ]);

        return redirect()
            ->route('class-sessions.index', $groupClass)
            ->with('success', 'Sesion reprogramada correctamente.');
    }

    public function cancel(
        Request $request,
        GroupClass $groupClass,
        ClassSession $session
    ) {
        $this->ensureSessionBelongsToGroupClass(
            $groupClass,
            $session
        );

        if ($session->status === ClassSessionStatus::Completed) {
            return back()->withErrors([
                'change_reason' => 'Una sesion completada no puede ser cancelada.',
            ]);
        }

        if ($session->status === ClassSessionStatus::Cancelled) {
            return back()->withErrors([
                'change_reason' => 'La sesion ya se encuentra cancelada.',
            ]);
        }

        $validated = $request->validate([
            'change_reason' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $session->update([
            'status' => ClassSessionStatus::Cancelled,
            'change_reason' => $validated['change_reason'],
        ]);

        return redirect()
            ->route('class-sessions.index', $groupClass)
            ->with('success', 'Sesion cancelada correctamente.');
    }

    private function ensureSessionBelongsToGroupClass(
        GroupClass $groupClass,
        ClassSession $session
    ): void {
        abort_if(
            $session->group_class_id !== $groupClass->id,
            404
        );
    }
}