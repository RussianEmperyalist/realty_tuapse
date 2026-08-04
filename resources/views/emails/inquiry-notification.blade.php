<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0; padding:24px; background:#f4f6fb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:720px; margin:0 auto; background:#ffffff; border:1px solid #dce5f0; border-radius:16px; overflow:hidden;">
        <div style="padding:24px 28px; background:#143045; color:#ffffff;">
            <div style="font-size:24px; font-weight:700; line-height:1.3;">{{ $headline }}</div>
            @if ($lead)
                <div style="margin-top:8px; font-size:14px; line-height:1.6; color:#d7e7f6;">{{ $lead }}</div>
            @endif
        </div>

        <div style="padding:24px 28px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                @foreach ($fields as $field)
                    <tr>
                        <td style="width:220px; padding:10px 0; vertical-align:top; font-weight:700; color:#143045;">
                            {{ $field['label'] }}
                        </td>
                        <td style="padding:10px 0; vertical-align:top; color:#1f2937;">
                            {!! nl2br(e($field['value'] !== null && $field['value'] !== '' ? $field['value'] : '—')) !!}
                        </td>
                    </tr>
                @endforeach
            </table>

            @if ($messageBody)
                <div style="margin-top:24px; padding-top:20px; border-top:1px solid #e5e7eb;">
                    <div style="margin-bottom:10px; font-weight:700; color:#143045;">Сообщение</div>
                    <div style="line-height:1.7; white-space:pre-line;">{{ $messageBody }}</div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
