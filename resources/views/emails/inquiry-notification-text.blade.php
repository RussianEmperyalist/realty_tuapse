{{ $headline }}

@if ($lead)
{{ $lead }}

@endif
@foreach ($fields as $field)
{{ $field['label'] }}: {{ $field['value'] !== null && $field['value'] !== '' ? $field['value'] : '—' }}
@endforeach

@if ($messageBody)
Сообщение:
{{ $messageBody }}
@endif
