<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Vencimiento de Membresías</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #333333;
            margin: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #dc3545;
            font-size: 18pt;
        }
        .header p {
            margin: 4px 0 0 0;
            color: #666666;
            font-size: 9pt;
        }
        .summary-box {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
            padding: 5px 8px;
            background-color: #e9ecef;
            border-left: 4px solid #0d6efd;
        }
        .section-title.expiring {
            border-left-color: #ffc107;
            background-color: #fff8e6;
        }
        .section-title.expired {
            border-left-color: #dc3545;
            background-color: #fbebe8;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cccccc;
            padding: 6px 7px;
        }
        table.data-table th {
            background-color: #f1f3f5;
            color: #212529;
            text-align: left;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 3px;
        }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; color: #ffffff; }
        .badge-success { background-color: #198754; color: #ffffff; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 8pt;
            color: #888888;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>REPORTE DE VENCIMIENTO DE MEMBRESÍAS</h1>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary-box">
        <strong>Resumen de Control:</strong><br>
        • Membresías activas por vencer (Próximos 7 días): <strong>{{ $report['expiringCount'] }}</strong><br>
        • Membresías vencidas en catálogo/periodo: <strong>{{ $report['expiredCount'] }}</strong>
    </div>

    {{-- SECCIÓN 1: POR VENCER (PRÓXIMOS 7 DÍAS) --}}
    <div class="section-title expiring">
        Membresías Activas por Vencer (Próximos 7 Días) - Total: {{ $report['expiringCount'] }}
    </div>

    @if($report['expiringActive']->isEmpty())
        <p>No se registran membresías activas con vencimiento en los próximos 7 días.</p>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th>Socio</th>
                    <th>Correo</th>
                    <th>Plan</th>
                    <th>Inicio</th>
                    <th>Vencimiento</th>
                    <th class="text-center">Estado</th>
                    <th class="text-right">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @php $today = \Carbon\Carbon::today(); @endphp
                @foreach($report['expiringActive'] as $m)
                    @php
                        $endDate = \Carbon\Carbon::parse($m->end_date);
                        $daysLeft = (int) $today->diffInDays($endDate, false);
                    @endphp
                    <tr>
                        <td><strong>{{ $m->member?->user?->first_name }} {{ $m->member?->user?->last_name }}</strong></td>
                        <td>{{ $m->member?->user?->email }}</td>
                        <td>{{ $m->plan?->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($m->start_date)->format('d/m/Y') }}</td>
                        <td><strong>{{ $endDate->format('d/m/Y') }}</strong></td>
                        <td class="text-center"><span class="badge badge-success">Activa</span></td>
                        <td class="text-right">
                            <span class="badge badge-warning">
                                {{ $daysLeft > 0 ? "Faltan {$daysLeft} días" : "Vence hoy" }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- SECCIÓN 2: VENCIDAS --}}
    <div class="section-title expired">
        Membresías Vencidas - Total: {{ $report['expiredCount'] }}
    </div>

    @if($report['expired']->isEmpty())
        <p>No se registran membresías vencidas para el rango seleccionado.</p>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th>Socio</th>
                    <th>Correo</th>
                    <th>Plan</th>
                    <th>Inicio</th>
                    <th>Vencimiento</th>
                    <th class="text-center">Estado</th>
                    <th class="text-right">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @php $today = \Carbon\Carbon::today(); @endphp
                @foreach($report['expired'] as $m)
                    @php
                        $endDate = \Carbon\Carbon::parse($m->end_date);
                        $daysExpired = (int) $endDate->diffInDays($today, false);
                    @endphp
                    <tr>
                        <td>{{ $m->member?->user?->first_name }} {{ $m->member?->user?->last_name }}</td>
                        <td>{{ $m->member?->user?->email }}</td>
                        <td>{{ $m->plan?->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($m->start_date)->format('d/m/Y') }}</td>
                        <td>{{ $endDate->format('d/m/Y') }}</td>
                        <td class="text-center"><span class="badge badge-danger">{{ $m->status?->label() ?? 'Vencida' }}</span></td>
                        <td class="text-right">
                            <span class="badge badge-danger">
                                {{ $daysExpired > 0 ? "Hace {$daysExpired} días" : "Vencida hoy" }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Sistema de Gestión de Gimnasio - Informe Oficial Admin</p>
    </div>

</body>
</html>
