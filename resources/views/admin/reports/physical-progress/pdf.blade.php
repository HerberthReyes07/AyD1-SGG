<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h1 {
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        img {
            width: 100%;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <h1>Historial de progreso físico</h1>
    <p><strong>Socio:</strong> {{ $member->user->first_name }} {{ $member->user->last_name }}</p>
    <p><strong>Período:</strong> {{ $startDate ?? 'Inicio del historial' }} — {{ $endDate ?? 'Actualidad' }}</p>

    @if ($chartImage)
    <img src="{{ $chartImage }}" alt="Gráfico de progreso">
    @endif

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Peso (kg)</th>
                <th>Cintura (cm)</th>
                <th>Brazo (cm)</th>
                <th>Pierna (cm)</th>
                <th>Entrenador</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($measurements as $measurement)
            <tr>
                <td>{{ $measurement->date->format('d/m/Y') }}</td>
                <td>{{ $measurement->weight }}</td>
                <td>{{ $measurement->waist_measurement ?? '—' }}</td>
                <td>{{ $measurement->arm_measurement ?? '—' }}</td>
                <td>{{ $measurement->leg_measurement ?? '—' }}</td>
                <td>
                    {{ $measurement->trainerAssignment->trainer->user->first_name }}
                    {{ $measurement->trainerAssignment->trainer->user->last_name }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
