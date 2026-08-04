<?php

namespace App\Support;

use App\Mail\InquiryNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class InquiryDeliveryService
{
    /**
     * Deliver an inquiry notification and update the inquiry delivery state.
     *
     * @param  array<int, array{label: string, value: string|null}>  $fields
     */
    public function deliver(
        Request $request,
        Model $inquiry,
        string $subject,
        string $headline,
        ?string $lead,
        array $fields,
        ?string $messageBody,
        string $successMessage,
        ?RedirectResponse $successRedirect = null,
    ): RedirectResponse {
        try {
            $mailable = new InquiryNotification(
                subjectLine: $subject,
                headline: $headline,
                lead: $lead,
                fields: $fields,
                messageBody: $messageBody,
            );

            $mail = Mail::to((string) $inquiry->recipient_email);
            $replyToEmail = trim((string) $request->input('email'));
            $replyToName = trim((string) $request->input('name'));

            if ($replyToEmail !== '') {
                $mail->send(
                    $mailable->replyTo(
                        address: $replyToEmail,
                        name: $replyToName !== '' ? $replyToName : null,
                    ),
                );
            } else {
                $mail->send($mailable);
            }

            $inquiry->forceFill([
                'delivery_status' => 'sent',
                'delivery_error' => null,
                'sent_at' => now(),
            ])->save();

            return ($successRedirect ?? back())->with('status', $successMessage);
        } catch (Throwable $exception) {
            report($exception);

            $inquiry->forceFill([
                'delivery_status' => 'failed',
                'delivery_error' => Str::limit($exception->getMessage(), 2000),
            ])->save();

            return back()
                ->withInput()
                ->withErrors([
                    'form' => 'Не удалось отправить заявку. Проверьте почтовые настройки и попробуйте еще раз.',
                ]);
        }
    }
}
