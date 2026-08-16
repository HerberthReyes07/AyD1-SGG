<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Reporte de Pases de Invitado</title>

    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
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
            font-size: 20px;
        }

        .header p {
            margin-top: 5px;
            color: #666666;
        }

        .filters {
            background-color: #f8f9fa;
            border: 1px solid #dddddd;
            padding: 10px;
            margin-bottom: 15px;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .summary td {
            border: 1px solid #dddddd;
            padding: 10px;
            text-align: center;
        }

        .summary-label {
            color: #666666;
            font-size: 9px;
        }

        .summary-value {
            color: #0d6efd;
            font-weight: bold;
            font-size: 16px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #cccccc;
            padding: 6px;
        }

        .data-table th {
            background-color: #0d6efd;
            color: white;
            text-align: left;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .footer {
            margin-top: 25px;
            text-align: right;
            color: #888888;
            font-size: 8px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>REPORTE DE PASES DE INVITADO</h1>

        <p>
            Dia de prueba / invitados |
            Generado el {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <div class="filters">
        <strong>Filtros aplicados:</strong>

        Desde: {{ $filters['date_from'] ?? 'Sin limite' }}
        |
        Hasta: {{ $filters['date_to'] ?? 'Sin limite' }}
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="summary-label">Pases registrados</div>
                <div class="summary-value">{{ $summary['total'] }}</div>
            </td>

            <td>
                <div class="summary-label">Dias con registro</div>
                <div class="summary-value">{{ $summary['active_days'] }}</div>
            </td>

            <td>
                <div class="summary-label">Promedio por dia</div>
                <div class="summary-value">{{ number_format($summary['average_per_day'], 1) }}</div>
            </td>
        </tr>
    </table>

    @if ($records->isEmpty())
        <p>No existen registros para los filtros seleccionados.</p>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th>Invitado</th>
                    <th>DPI</th>
                    <th>Fecha de visita</th>
                    <th>Registrado por</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($records as $guestPass)
                    <tr>
                        <td>{{ $guestPass->guest_name }}</td>
                        <td>{{ $guestPass->dpi }}</td>
                        <td>{{ $guestPass->visit_date->format('d/m/Y') }}</td>
                        <td>
                            @if ($guestPass->registeredBy)
                                {{ $guestPass->registeredBy->first_name }} {{ $guestPass->registeredBy->last_name }}
                            @else
                                --
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Sistema de Gestion de Gimnasio - Reporte de pases de invitado
    </div>

</body>

</html>
