<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RoutineService;
use App\Services\PeriodicMeasurementService;
use App\Services\TrainerAssignmentService;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    private const ROUTINE_COLORS = ['primary', 'success', 'warning', 'danger', 'info', 'dark'];

    public function __construct(
        private RoutineService $routineService,
        private PeriodicMeasurementService $measurementService,
        private TrainerAssignmentService $assignmentService
    ) {}

    public function index()
    {
        $member = Auth::user()->member;

        $includesTrainer = $member->currentMembershipIncludesTrainer();
        $hasActiveAssignment = $member->hasActiveTrainerAssignment();
        $hasHistory = $member->hasAnyTrainerAssignmentHistory();

        // Estado 1: nunca tuvo entrenador y su plan actual no lo incluye
        if (!$includesTrainer && !$hasHistory) {
            return view('member.training.index', [
                'accessState' => 'upgrade_required',
                'currentAssignment' => null,
                'routines' => collect(),
                'exercisesByDay' => collect(),
            ]);
        }

        // Estado 2: plan incluye entrenador, pero aún no se lo asignan
        if ($includesTrainer && !$hasActiveAssignment && !$hasHistory) {
            return view('member.training.index', [
                'accessState' => 'pending_assignment',
                'currentAssignment' => null,
                'routines' => collect(),
                'exercisesByDay' => collect(),
            ]);
        }

        $currentAssignment = $member->trainerAssignments()
            ->whereNull('end_date')
            ->with(['trainer.user', 'trainer.specialty'])
            ->first();

        $routines = collect();
        $exercisesByDay = collect();
        $measurements = collect();
        $chartData = ['labels' => [], 'weight' => [], 'waist' => [], 'arm' => [], 'leg' => []];
        $trainerHistory = $this->assignmentService->getAllForMemberWithRatings($member->user_id);

        if ($currentAssignment) {
            $routines = $this->routineService->getActiveForAssignment($currentAssignment->id)
                ->values()
                ->map(function ($routine, $index) {
                    $routine->color = self::ROUTINE_COLORS[$index % count(self::ROUTINE_COLORS)];
                    return $routine;
                });

            $exercisesByDay = collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])
                ->mapWithKeys(function ($day) use ($routines) {
                    $items = collect();
                    foreach ($routines as $routine) {
                        foreach ($routine->routineExercises->where('suggested_day.value', $day) as $exercise) {
                            $items->push(['routine' => $routine, 'exercise' => $exercise]);
                        }
                    }
                    return [$day => $items];
                });
        }

        if ($currentAssignment || $hasHistory) {
            $measurements = $this->measurementService->getHistoryForAssignment($currentAssignment?->id ?? 0);

            $fullHistory = $this->measurementService->getFullHistoryForMember($member->user_id);

            $chartData = [
                'labels' => $fullHistory->pluck('date')->map(fn($d) => $d->format('d/m/Y'))->toArray(),
                'weight' => $fullHistory->pluck('weight')->toArray(),
                'waist' => $fullHistory->pluck('waist_measurement')->toArray(),
                'arm' => $fullHistory->pluck('arm_measurement')->toArray(),
                'leg' => $fullHistory->pluck('leg_measurement')->toArray(),
            ];
        }

        return view('member.training.index', [
            'accessState' => $currentAssignment ? 'active' : 'history_only',
            'currentAssignment' => $currentAssignment,
            'routines' => $routines,
            'exercisesByDay' => $exercisesByDay,
            'measurements' => $measurements,
            'chartData' => $chartData,
            'trainerHistory' => $trainerHistory,
        ]);
    }
}
