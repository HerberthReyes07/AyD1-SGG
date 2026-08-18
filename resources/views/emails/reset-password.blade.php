<x-mail::message>
# Restablecer contraseña

Hola,

Recibimos una solicitud para restablecer la contraseña de tu cuenta en **{{ config('app.name') }}**. Haz clic en el siguiente botón para elegir una nueva contraseña:

<x-mail::button :url="$url" color="primary">
Restablecer contraseña
</x-mail::button>

Este enlace vence en **{{ $expireMinutes }} minutos**.

Si no solicitaste este cambio, no es necesario que hagas nada, tu contraseña actual sigue funcionando con normalidad.

Saludos,<br>
Equipo de {{ config('app.name') }}

<x-slot:subcopy>
Si tienes problemas para hacer clic en el botón "Restablecer contraseña", copia y pega la siguiente URL en tu navegador: [{{ $url }}]({{ $url }})
</x-slot:subcopy>
</x-mail::message>
