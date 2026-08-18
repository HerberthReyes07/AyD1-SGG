<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MembershipExpirationExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private array $reportData) {}

    public function collection(): Collection
    {
        $rows = collect();
        $today = Carbon::today();

        // 1. Membresías activas por vencer (Próximos 7 días)
        foreach ($this->reportData['expiringActive'] as $m) {
            $endDate = Carbon::parse($m->end_date);
            $daysLeft = (int) $today->diffInDays($endDate, false);

            $rows->push([
                'Por Vencer (Próx. 7 días)',
                $m->member?->user?->first_name . ' ' . $m->member?->user?->last_name,
                $m->member?->user?->email,
                $m->plan?->name ?? 'Sin Plan',
                Carbon::parse($m->start_date)->format('d/m/Y'),
                $endDate->format('d/m/Y'),
                $m->status?->label() ?? (string) $m->status,
                $daysLeft > 0 ? "Faltan {$daysLeft} días" : "Vence hoy",
            ]);
        }

        // 2. Membresías vencidas
        foreach ($this->reportData['expired'] as $m) {
            $endDate = Carbon::parse($m->end_date);
            $daysExpired = (int) $endDate->diffInDays($today, false);

            $rows->push([
                'Vencida',
                $m->member?->user?->first_name . ' ' . $m->member?->user?->last_name,
                $m->member?->user?->email,
                $m->plan?->name ?? 'Sin Plan',
                Carbon::parse($m->start_date)->format('d/m/Y'),
                $endDate->format('d/m/Y'),
                $m->status?->label() ?? (string) $m->status,
                $daysExpired > 0 ? "Vencida hace {$daysExpired} días" : "Vencida hoy",
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Sección de Reporte',
            'Socio',
            'Correo Electrónico',
            'Plan de Membresía',
            'Fecha Inicio',
            'Fecha Fin / Vencimiento',
            'Estado Actual',
            'Detalle Días',
        ];
    }
}
