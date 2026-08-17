<x-mail::message>
# Código de verificación

Hola **{{ $name }}**,

Recibimos una solicitud para iniciar sesión en tu cuenta de **{{ config('app.name') }}**. Usa el siguiente código para completar la verificación:

<div class="otp-code">{{ $code }}</div>

Este código vence en **10 minutos**.

Si no intentaste iniciar sesión, puedes ignorar este mensaje con tranquilidad, tu cuenta sigue segura.

Saludos,<br>
Equipo de {{ config('app.name') }}
</x-mail::message>
