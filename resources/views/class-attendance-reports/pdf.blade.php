<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>
        Reporte de Asistencia por Clase
    </title>

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
        <h1>
            REPORTE DE ASISTENCIA POR CLASE
        </h1>

        <p>
            Clases grupales |
            Generado el {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>


    <div class="filters">

        <strong>Filtros aplicados:</strong>

        Desde:
        {{ $filters['date_from'] ?? 'Sin limite' }}

        |

        Hasta:
        {{ $filters['date_to'] ?? 'Sin limite' }}

        |

        Clase:
        {{ $filters['group_class_name'] ?? 'Todas' }}

    </div>


    <table class="summary">

        <tr>

            <td>
                <div class="summary-label">
                    Participantes
                </div>

                <div class="summary-value">
                    {{ $summary['total'] }}
                </div>
            </td>

            <td>
                <div class="summary-label">
                    Asistieron
                </div>

                <div class="summary-value">
                    {{ $summary['attended'] }}
                </div>
            </td>

            <td>
                <div class="summary-label">
                    No asistieron
                </div>

                <div class="summary-value">
                    {{ $summary['no_show'] }}
                </div>
            </td>

            <td>
                <div class="summary-label">
                    Porcentaje asistencia
                </div>

                <div class="summary-value">
                    {{
                        number_format(
                            $summary['percentage'],
                            2
                        )
                    }}%
                </div>
            </td>

        </tr>

    </table>


    @if ($records->isEmpty())

        <p>
            No existen registros para los filtros
            seleccionados.
        </p>

    @else

        <table class="data-table">

            <thead>

                <tr>
                    <th>Clase</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Socio</th>
                    <th>Correo</th>
                    <th>Check-in</th>
                    <th>Estado</th>
                </tr>

            </thead>

            <tbody>

                @foreach ($records as $enrollment)

                    <tr>

                        <td>
                            {{
                                $enrollment
                                    ->classSession
                                    ->groupClass
                                    ->name
                            }}
                        </td>

                        <td>
                            {{
                                $enrollment
                                    ->classSession
                                    ->starts_at
                                    ->format('d/m/Y')
                            }}
                        </td>

                        <td>
                            {{
                                $enrollment
                                    ->classSession
                                    ->starts_at
                                    ->format('H:i')
                            }}
                        </td>

                        <td>
                            {{
                                $enrollment
                                    ->member
                                    ->user
                                    ->first_name
                            }}

                            {{
                                $enrollment
                                    ->member
                                    ->user
                                    ->last_name
                            }}
                        </td>

                        <td>
                            {{
                                $enrollment
                                    ->member
                                    ->user
                                    ->email
                            }}
                        </td>

                        <td>

                            @if ($enrollment->classAttendance)

                                {{
                                    $enrollment
                                        ->classAttendance
                                        ->check_in_at
                                        ->format(
                                            'd/m/Y H:i:s'
                                        )
                                }}

                            @else

                                --

                            @endif

                        </td>

                        <td>

                            @if (
                                $enrollment->status ===
                                \App\Enums\ClassEnrollmentStatus::Attended
                            )

                                Asistio

                            @else

                                No asistio

                            @endif

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    @endif


    <div class="footer">
        Sistema de Gestion de Gimnasio -
        Reporte de clases grupales
    </div>

</body>

</html>