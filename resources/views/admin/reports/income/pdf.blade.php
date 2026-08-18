<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ingresos</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            color: #333333;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #0d6efd;
            font-size: 20pt;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666666;
            font-size: 10pt;
        }
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 20px;
        }
        .summary-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 6px;
            font-size: 10pt;
        }
        .summary-value {
            font-weight: bold;
            font-size: 13pt;
            color: #0d6efd;
        }
        .chart-container {
            text-align: center;
            margin-bottom: 25px;
        }
        .chart-container img {
            max-width: 100%;
            height: auto;
            border: 1px solid #dddddd;
            border-radius: 4px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cccccc;
            padding: 7px 8px;
        }
        table.data-table th {
            background-color: #0d6efd;
            color: #ffffff;
            text-align: left;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        table.data-table tfoot td {
            background-color: #343a40;
            color: #ffffff;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 8pt;
            color: #888888;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>REPORTE DE INGRESOS</h1>
        <p>Agrupación: {{ $report['groupBy'] === 'week' ? 'Semanal' : 'Mensual' }} | Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary-box">
        <table>
            <tr>
                <td><strong>Filtros aplicados:</strong> {{ $report['startDate'] ?? 'Inicio' }} a {{ $report['endDate'] ?? 'Hoy' }}</td>
                <td class="text-right"><strong>Total Recaudado:</strong> <span class="summary-value">Q {{ number_format($report['totalIncome'], 2) }}</span></td>
            </tr>
            <tr>
                <td><strong>Total de Transacciones:</strong> {{ $report['totalPaymentsCount'] }}</td>
                <td class="text-right"><strong>Promedio por Pago:</strong> Q {{ $report['totalPaymentsCount'] > 0 ? number_format($report['totalIncome'] / $report['totalPaymentsCount'], 2) : '0.00' }}</td>
            </tr>
        </table>
    </div>

    @if(!empty($chartImage))
        <div class="chart-container">
            <p><strong>Evolución de Ingresos</strong></p>
            <img src="{{ $chartImage }}" alt="Gráfico de Ingresos">
        </div>
    @endif

    <h3>Desglose por Planes de Membresía</h3>

    @if(count($report['periods']) === 0)
        <p class="text-center">No se encontraron pagos registrados en el periodo seleccionado.</p>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th>Periodo ({{ $report['groupBy'] === 'week' ? 'Semana' : 'Mes' }})</th>
                    <th class="text-center">Pagos</th>
                    <th class="text-right">Total</th>
                    @foreach($report['plans'] as $plan)
                        <th class="text-right">{{ $plan->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($report['periods'] as $period)
                    <tr>
                        <td>{{ $period['period_label'] }}</td>
                        <td class="text-center">{{ $period['payment_count'] }}</td>
                        <td class="text-right"><strong>Q {{ number_format($period['total'], 2) }}</strong></td>
                        @foreach($report['plans'] as $plan)
                            <td class="text-right">Q {{ number_format($period['plans'][$plan->id] ?? 0, 2) }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL GENERAL</td>
                    <td class="text-center">{{ $report['totalPaymentsCount'] }}</td>
                    <td class="text-right">Q {{ number_format($report['totalIncome'], 2) }}</td>
                    @foreach($report['plans'] as $plan)
                        <td class="text-right">Q {{ number_format($report['incomeByPlan'][$plan->id]['total'] ?? 0, 2) }}</td>
                    @endforeach
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="footer">
        <p>Sistema de Gestión de Gimnasio - Informe Oficial Admin</p>
    </div>

</body>
</html>
