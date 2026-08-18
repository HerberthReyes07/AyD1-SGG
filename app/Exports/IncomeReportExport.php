<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IncomeReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private array $reportData) {}

    public function collection(): Collection
    {
        $rows = collect();
        $plans = $this->reportData['plans'];
        $periods = $this->reportData['periods'];

        foreach ($periods as $period) {
            $row = [
                $period['period_label'],
                $period['payment_count'],
                number_format($period['total'], 2, '.', ''),
            ];

            foreach ($plans as $plan) {
                $planAmount = $period['plans'][$plan->id] ?? 0.0;
                $row[] = number_format($planAmount, 2, '.', '');
            }

            $rows->push($row);
        }

        // Row for summary totals
        $summaryRow = [
            'TOTAL GENERAL',
            $this->reportData['totalPaymentsCount'],
            number_format($this->reportData['totalIncome'], 2, '.', ''),
        ];

        foreach ($plans as $plan) {
            $totalPlan = $this->reportData['incomeByPlan'][$plan->id]['total'] ?? 0.0;
            $summaryRow[] = number_format($totalPlan, 2, '.', '');
        }

        $rows->push($summaryRow);

        return $rows;
    }

    public function headings(): array
    {
        $headings = [
            'Periodo (' . ($this->reportData['groupBy'] === 'week' ? 'Semana' : 'Mes') . ')',
            'Cantidad de Pagos',
            'Ingreso Total',
        ];

        foreach ($this->reportData['plans'] as $plan) {
            $headings[] = 'Plan: ' . $plan->name;
        }

        return $headings;
    }
}
