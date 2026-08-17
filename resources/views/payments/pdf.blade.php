<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pago #{{ $payment->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #333333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #0d6efd;
            margin: 0;
        }
        .subtitle {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }
        .receipt-info {
            text-align: right;
        }
        .receipt-id {
            font-size: 16px;
            font-weight: bold;
            color: #212529;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-left: 4px solid #0d6efd;
            margin-top: 20px;
            margin-bottom: 12px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 6px 10px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #495057;
            width: 35%;
        }
        .financial-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        .financial-table th {
            background-color: #e9ecef;
            color: #212529;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #dee2e6;
        }
        .financial-table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .text-right {
            text-align: right;
        }
        .total-row td {
            font-size: 16px;
            font-weight: bold;
            background-color: #f1f3f5;
            border-top: 2px solid #212529;
        }
        .discount-text {
            color: #dc3545;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 11px;
            color: #6c757d;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="title">GIMNASIO SGG</h1>
                    <div class="subtitle">Comprobante Oficial de Pago de Membresía</div>
                </td>
                <td class="receipt-info">
                    <div class="receipt-id">Comprobante #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div><strong>Fecha:</strong> {{ $payment->payment_date?->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Datos del Socio -->
    <div class="section-title">Información del Socio</div>
    <table class="info-table">
        <tr>
            <td class="label">Socio:</td>
            <td>
                {{ $payment->memberMembership?->member?->user?->first_name }}
                {{ $payment->memberMembership?->member?->user?->last_name }}
            </td>
        </tr>
        <tr>
            <td class="label">Correo Electrónico:</td>
            <td>{{ $payment->memberMembership?->member?->user?->email }}</td>
        </tr>
    </table>

    <!-- Datos de la Membresía -->
    <div class="section-title">Detalles de la Membresía</div>
    <table class="info-table">
        <tr>
            <td class="label">Membresía ID:</td>
            <td>#{{ $payment->member_membership_id }}</td>
        </tr>
        <tr>
            <td class="label">Plan Contratado:</td>
            <td>{{ $payment->memberMembership?->plan?->name }}</td>
        </tr>
        <tr>
            <td class="label">Duración del Plan:</td>
            <td>{{ $payment->memberMembership?->plan?->duration_months }} mes(es)</td>
        </tr>
        <tr>
            <td class="label">Vigencia:</td>
            <td>
                {{ $payment->memberMembership?->start_date?->format('d/m/Y') }} al
                {{ $payment->memberMembership?->end_date?->format('d/m/Y') }}
            </td>
        </tr>
        <tr>
            <td class="label">Método de Pago:</td>
            <td>{{ $payment->paymentMethod?->name ?? 'No especificado' }}</td>
        </tr>
        <tr>
            <td class="label">Registrado Por:</td>
            <td>
                {{ $payment->registeredBy?->first_name }} {{ $payment->registeredBy?->last_name }}
            </td>
        </tr>
    </table>

    <!-- Desglose Financiero -->
    <div class="section-title">Desglose del Cobro</div>
    <table class="financial-table">
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Precio Base del Plan ({{ $payment->memberMembership?->plan?->name }})</td>
                <td class="text-right">Q{{ number_format($payment->memberMembership?->plan?->price, 2) }}</td>
            </tr>
            @if ($payment->promotion)
                <tr>
                    <td>
                        Descuento Aplicado: <strong>{{ $payment->promotion->name }}</strong>
                        <br>
                        <small style="color: #6c757d;">
                            (Tipo: {{ $payment->promotion->type->label() }} - Valor:
                            {{ $payment->promotion->type->value === 'percentage' ? number_format($payment->promotion->value, 0) . '%' : 'Q' . number_format($payment->promotion->value, 2) }})
                        </small>
                    </td>
                    <td class="text-right discount-text">
                        -Q{{ number_format(max(0, ($payment->memberMembership?->plan?->price ?? 0) - $payment->amount), 2) }}
                    </td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Total Pagado</td>
                <td class="text-right">Q{{ number_format($payment->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Gracias por su preferencia. Este documento sirve como comprobante legal de pago.</p>
        <p>Gimnasio SGG &copy; {{ date('Y') }} - Todos los derechos reservados.</p>
    </div>

</body>
</html>
