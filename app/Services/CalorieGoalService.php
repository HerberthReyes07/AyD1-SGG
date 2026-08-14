<?php

namespace App\Services;

use App\Models\CalorieGoal;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CalorieGoalService
{
    // cierra la meta vigente y crea una nueva desde hoy, sea el socio o el entrenador quien la defina
    public function setGoal(Member $member, array $data, ?int $definedBy = null): CalorieGoal
    {
        return DB::transaction(function () use ($member, $data, $definedBy) {
            $startDate = today();

            $current = $this->getCurrentGoal($member, $startDate);

            if ($current) {
                $current->update([
                    'end_date' => $startDate->copy()->subDay(),
                ]);
            }

            return CalorieGoal::create([
                'daily_calories' => $data['daily_calories'],
                'objective' => $data['objective'],
                'start_date' => $startDate,
                'end_date' => null,
                'member_id' => $member->user_id,
                'defined_by' => $definedBy,
            ]);
        });
    }

    public function getCurrentGoal(Member $member, ?Carbon $onDate = null): ?CalorieGoal
    {
        $onDate ??= today();

        return CalorieGoal::where('member_id', $member->user_id)
            ->whereDate('start_date', '<=', $onDate)
            ->where(function ($query) use ($onDate) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $onDate);
            })
            ->orderByDesc('start_date')
            ->first();
    }

    // compara el consumo del dia contra la meta, +-10% cuenta como dentro de rango
    public function compare(float $consumedCalories, CalorieGoal $goal): array
    {
        $goalCalories = (float) $goal->daily_calories;
        $lowerBound = $goalCalories * 0.9;
        $upperBound = $goalCalories * 1.1;

        $status = match (true) {
            $consumedCalories < $lowerBound => 'below',
            $consumedCalories > $upperBound => 'above',
            default => 'within',
        };

        return [
            'status' => $status,
            'percentage' => $goalCalories > 0 ? round(($consumedCalories / $goalCalories) * 100, 1) : 0.0,
            'consumed' => round($consumedCalories, 2),
            'goal' => $goalCalories,
        ];
    }
}
