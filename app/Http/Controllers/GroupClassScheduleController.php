<?php

namespace App\Http\Controllers;

use App\Enums\Weekday;
use App\Models\GroupClass;
use App\Models\GroupClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GroupClassScheduleController extends Controller
{
    public function index(GroupClass $groupClass)
    {
        $groupClass->load([
            'category',
            'trainer.user',
        ]);

        $schedules = $groupClass->schedules()
            ->orderByRaw("
                CASE weekday
                    WHEN 'monday' THEN 1
                    WHEN 'tuesday' THEN 2
                    WHEN 'wednesday' THEN 3
                    WHEN 'thursday' THEN 4
                    WHEN 'friday' THEN 5
                    WHEN 'saturday' THEN 6
                    WHEN 'sunday' THEN 7
                END
            ")
            ->orderBy('start_time')
            ->get();

        $weekdays = Weekday::cases();

        return view('group-class-schedules.index', compact(
            'groupClass',
            'schedules',
            'weekdays'
        ));
    }

    public function store(Request $request, GroupClass $groupClass)
    {
        $validated = $request->validate([
            'weekday' => [
                'required',
                Rule::enum(Weekday::class),
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
            ],
        ]);

        $startTime = $validated['start_time'] . ':00';

        $exists = GroupClassSchedule::where(
            'group_class_id',
            $groupClass->id
        )
            ->where('weekday', $validated['weekday'])
            ->where('start_time', $startTime)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' => 'Este horario ya esta registrado para la clase.',
                ]);
        }

        $groupClass->schedules()->create([
            'weekday' => $validated['weekday'],
            'start_time' => $startTime,
        ]);

        return redirect()
            ->route('group-class-schedules.index', $groupClass)
            ->with('success', 'Horario agregado correctamente.');
    }

    public function update(
    Request $request,
    GroupClass $groupClass,
    GroupClassSchedule $schedule
    ) {
        abort_if(
            $schedule->group_class_id !== $groupClass->id,
            404
        );

        $validated = $request->validate([
            'weekday' => [
                'required',
                Rule::enum(Weekday::class),
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
            ],
        ]);

        $startTime = $validated['start_time'] . ':00';

        $exists = GroupClassSchedule::where(
            'group_class_id',
            $groupClass->id
        )
            ->where('weekday', $validated['weekday'])
            ->where('start_time', $startTime)
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' => 'Este horario ya esta registrado para la clase.',
                ]);
        }

        $schedule->update([
            'weekday' => $validated['weekday'],
            'start_time' => $startTime,
        ]);

        return redirect()
            ->route('group-class-schedules.index', $groupClass)
            ->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy(
        GroupClass $groupClass,
        GroupClassSchedule $schedule
    ) {
        abort_if(
            $schedule->group_class_id !== $groupClass->id,
            404
        );

        $schedule->delete();

        return redirect()
            ->route('group-class-schedules.index', $groupClass)
            ->with('success', 'Horario eliminado correctamente.');
    }
}