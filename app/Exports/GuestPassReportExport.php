<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GuestPassReportExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(
        private Collection $records
    ) {}

    public function collection(): Collection
    {
        return $this->records->map(function ($guestPass) {
            return [
                $guestPass->guest_name,
                $guestPass->dpi,
                $guestPass->visit_date->format('d/m/Y'),
                $guestPass->registeredBy
                    ? $guestPass->registeredBy->first_name.' '.$guestPass->registeredBy->last_name
                    : '--',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Invitado',
            'DPI',
            'Fecha de visita',
            'Registrado por',
        ];
    }
}
