@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://admision.uni.edu.pe/wp-content/uploads/2026/01/escudo-uni.png" class="logo" alt="DIAD-UNI Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
