<?php

namespace App\Exports;

use App\Enums\ClassEnrollmentStatus;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassAttendanceReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(
        private Collection $records
    ) {}

    public function collection(): Collection
    {
        return $this->records->map(function ($enrollment) {
            $session = $enrollment->classSession;
            $groupClass = $session->groupClass;
            $user = $enrollment->member->user;

            return [
                $groupClass->name,
                $session->starts_at->format('d/m/Y'),
                $session->starts_at->format('H:i'),
                $user->first_name . ' ' . $user->last_name,
                $user->email,
                $enrollment->classAttendance
                    ? $enrollment->classAttendance
                        ->check_in_at
                        ->format('d/m/Y H:i:s')
                    : '--',
                $enrollment->status === ClassEnrollmentStatus::Attended
                    ? 'Asistio'
                    : 'No asistio',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Clase',
            'Fecha',
            'Hora',
            'Socio',
            'Correo',
            'Check-in',
            'Estado',
        ];
    }
}