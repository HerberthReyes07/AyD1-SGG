@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<span class="logo-badge">🏋️</span>
<div class="brand-name">{{ trim($slot) }}</div>
<div class="brand-tagline">Sistema de Gestión de Gimnasio</div>
</a>
</td>
</tr>
