<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PhysicalProgressExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $measurements) {}

    public function collection(): Collection
    {
        return $this->measurements->map(fn ($m) => [
            $m->date->format('d/m/Y'),
            $m->weight,
            $m->waist_measurement,
            $m->arm_measurement,
            $m->leg_measurement,
            $m->trainerAssignment->trainer->user->first_name . ' ' . $m->trainerAssignment->trainer->user->last_name,
        ]);
    }

    public function headings(): array
    {
        return ['Fecha', 'Peso (kg)', 'Cintura (cm)', 'Brazo (cm)', 'Pierna (cm)', 'Entrenador'];
    }
}
