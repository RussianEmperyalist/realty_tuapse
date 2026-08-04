<?php

namespace App\Support;

use App\Mail\InquiryNotification;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class OperationalMailService
{
    /**
     * Return the current delivery profile and its readiness for live SMTP.
     *
     * @return array{
     *     default_mailer:string,
     *     transport:string,
     *     host:string|null,
     *     port:int|string|null,
     *     scheme:string|null,
     *     from_address:string,
     *     from_name:string,
     *     recipient_email:string,
     *     test_email:string,
     *     ready:bool,
     *     issue:string|null
     * }
     */
    public function summary(): array
    {
        $defaultMailer = (string) config('mail.default', 'log');
        $mailerConfig = config("mail.mailers.{$defaultMailer}");
        $mailerConfig = is_array($mailerConfig) ? $mailerConfig : [];
        $transport = (string) ($mailerConfig['transport'] ?? $defaultMailer);
        $issue = $this->issueForLiveDelivery($defaultMailer, $transport, $mailerConfig);

        return [
            'default_mailer' => $defaultMailer,
            'transport' => $transport,
            'host' => isset($mailerConfig['host']) ? (string) $mailerConfig['host'] : null,
            'port' => $mailerConfig['port'] ?? null,
            'scheme' => isset($mailerConfig['scheme']) ? (string) $mailerConfig['scheme'] : null,
            'from_address' => (string) config('mail.from.address', ''),
            'from_name' => (string) config('mail.from.name', ''),
            'recipient_email' => (string) config('realty.contact_email', ''),
            'test_email' => (string) config('realty.mail_test_email', ''),
            'ready' => $issue === null,
            'issue' => $issue,
        ];
    }

    /**
     * Throw if the current mailer is not suitable for real external delivery.
     */
    public function ensureLiveDeliveryReady(): array
    {
        $summary = $this->summary();

        if ($summary['issue'] !== null) {
            throw new RuntimeException($summary['issue']);
        }

        return $summary;
    }

    /**
     * Send a live SMTP test message using the current mail profile.
     */
    public function sendTestMessage(string $to): void
    {
        $summary = $this->ensureLiveDeliveryReady();

        Mail::mailer($summary['default_mailer'])
            ->to($to)
            ->send(new InquiryNotification(
                subjectLine: 'SMTP test: Realty Tuapse',
                headline: 'Проверка почтовой отправки',
                lead: 'Это тестовое письмо подтверждает, что SMTP настроен и сайт может отправлять заявки.',
                fields: [
                    ['label' => 'Сайт', 'value' => (string) config('app.name')],
                    ['label' => 'Окружение', 'value' => (string) config('app.env')],
                    ['label' => 'Mailer', 'value' => $summary['default_mailer']],
                    ['label' => 'Transport', 'value' => $summary['transport']],
                    ['label' => 'From', 'value' => $summary['from_address']],
                    ['label' => 'Проверено', 'value' => now()->format('d.m.Y H:i:s')],
                ],
                messageBody: 'Если вы получили это письмо, боевой почтовый сценарий на сайте работает.',
            ));
    }

    /**
     * Detect whether the current mail profile is safe for live production delivery.
     */
    private function issueForLiveDelivery(string $defaultMailer, string $transport, array $mailerConfig): ?string
    {
        if ($mailerConfig === []) {
            return "MAIL_MAILER={$defaultMailer} указывает на несуществующий профиль.";
        }

        if (in_array($transport, ['log', 'array'], true)) {
            return "MAIL_MAILER={$defaultMailer} не отправляет письма наружу. Для боевого режима нужен smtp.";
        }

        if ($transport === 'failover') {
            $fallbackMailers = array_map('strval', (array) ($mailerConfig['mailers'] ?? []));

            if (array_intersect($fallbackMailers, ['log', 'array']) !== []) {
                return 'Failover с fallback на log/array скрывает сбои SMTP. Для боевого режима используйте MAIL_MAILER=smtp.';
            }
        }

        if ($transport === 'smtp') {
            $host = trim((string) ($mailerConfig['host'] ?? ''));
            $port = trim((string) ($mailerConfig['port'] ?? ''));
            $from = trim((string) config('mail.from.address', ''));
            $username = trim((string) ($mailerConfig['username'] ?? ''));
            $password = trim((string) ($mailerConfig['password'] ?? ''));

            if ($host === '' || $port === '' || $from === '') {
                return 'Для SMTP должны быть заполнены MAIL_HOST, MAIL_PORT и MAIL_FROM_ADDRESS.';
            }

            if (in_array(strtolower($host), ['127.0.0.1', 'localhost'], true)) {
                return 'Текущий SMTP-хост локальный. Для боевого режима укажите внешний SMTP-сервер.';
            }

            if (($username === '') xor ($password === '')) {
                return 'MAIL_USERNAME и MAIL_PASSWORD должны быть заполнены парой.';
            }
        }

        return null;
    }
}
