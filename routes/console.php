<?php

use App\Models\Employee;
use App\Models\GalleryAlbum;
use App\Models\GalleryItem;
use App\Models\NewsPost;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use App\Support\MediaPath;
use App\Support\OperationalMailService;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('realty:mail-test {to? : Recipient email for the SMTP test}', function (?string $to = null): int {
    $service = app(OperationalMailService::class);
    $summary = $service->summary();
    $target = $to ?: $summary['test_email'] ?: $summary['recipient_email'];

    $this->newLine();
    $this->info('Realty Tuapse SMTP test');
    $this->line('Mailer: ' . $summary['default_mailer']);
    $this->line('Transport: ' . $summary['transport']);
    $this->line('Host: ' . ($summary['host'] ?: 'n/a'));
    $this->line('Port: ' . ($summary['port'] ?: 'n/a'));
    $this->line('From: ' . $summary['from_address']);
    $this->line('To: ' . $target);
    $this->newLine();

    if ($summary['issue'] !== null) {
        $this->error($summary['issue']);

        return Command::FAILURE;
    }

    if (! is_string($target) || trim($target) === '') {
        $this->error('Recipient email is not set. Pass an email argument or configure MAIL_TEST_TO.');

        return Command::FAILURE;
    }

    try {
        $service->sendTestMessage($target);
        $this->info('Test email was sent successfully.');

        return Command::SUCCESS;
    } catch (\Throwable $exception) {
        report($exception);
        $this->error('SMTP test failed: ' . $exception->getMessage());

        return Command::FAILURE;
    }
})->purpose('Send a live SMTP test email for the Realty Tuapse project');

Artisan::command('realty:smoke-check', function (): int {
    $kernel = app(HttpKernel::class);
    $checks = [
        ['url' => '/', 'status' => 200],
        ['url' => '/contacts', 'status' => 200],
        ['url' => '/search', 'status' => 200],
        ['url' => '/news', 'status' => 200],
        ['url' => '/faq', 'status' => 200],
        ['url' => '/articles', 'status' => 200],
        ['url' => '/review', 'status' => 200],
        ['url' => '/favorites', 'status' => 200],
        ['url' => '/contact-us', 'status' => 200],
        ['url' => '/callback', 'status' => 200],
        ['url' => '/booking/request', 'status' => 200],
        ['url' => '/login', 'status' => 200],
        ['url' => '/register', 'status' => 200],
        ['url' => '/recover', 'status' => 200],
        ['url' => '/informaciya', 'status' => 200],
        ['url' => '/sitemap', 'status' => 200],
        ['url' => '/property/prodam-3-komkvartiru-centr', 'status' => 200],
        ['url' => '/specialoffers-3.html', 'status' => 301],
        ['url' => '/tuapse/kvartira.html', 'status' => 301],
        ['url' => '/service-2-2.html', 'status' => 301],
        ['url' => '/admin', 'status' => 302],
        ['url' => '/admin/inquiries', 'status' => 302],
    ];
    $mojibakePatterns = ['Р С’', 'Р РЋ', 'Р СњР Вµ', 'Р СџР С•', 'РІР‚', 'Р вЂњР В»Р В°Р Р†Р Р…'];
    $rows = [];
    $hasFailures = false;

    foreach ($checks as $check) {
        $request = Request::create($check['url'], 'GET');
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        $body = method_exists($response, 'getContent') ? (string) $response->getContent() : '';
        $mojibakeHits = 0;

        foreach ($mojibakePatterns as $pattern) {
            $mojibakeHits += substr_count($body, $pattern);
        }

        $location = $response->headers->get('Location', '');
        $result = ($status === $check['status'] && $mojibakeHits === 0) ? 'OK' : 'FAIL';

        if ($result !== 'OK') {
            $hasFailures = true;
        }

        $rows[] = [
            $check['url'],
            (string) $check['status'],
            (string) $status,
            $mojibakeHits > 0 ? (string) $mojibakeHits : '0',
            $location !== '' ? $location : '-',
            $result,
        ];

        $kernel->terminate($request, $response);
    }

    $this->table(
        ['URL', 'Expected', 'Actual', 'Mojibake', 'Location', 'Result'],
        $rows,
    );

    if ($hasFailures) {
        $this->error('Smoke-check found mismatches. Review the table above.');

        return Command::FAILURE;
    }

    $this->info('Smoke-check passed.');

    return Command::SUCCESS;
})->purpose('Run a pre-release smoke-check for the Realty Tuapse project');

Artisan::command('realty:media-audit', function (): int {
    $rows = [];
    $hasFailures = false;

    $addRow = function (string $type, string $identifier, string $field, ?string $path) use (&$rows, &$hasFailures): void {
        $normalizedPath = trim((string) $path);
        $status = $normalizedPath === '' ? 'MISSING' : (MediaPath::exists($normalizedPath) ? 'OK' : 'BROKEN');

        if ($status !== 'OK') {
            $hasFailures = true;
        }

        $rows[] = [
            $type,
            $identifier,
            $field,
            $normalizedPath !== '' ? $normalizedPath : '—',
            $status,
        ];
    };

    Employee::query()->orderBy('id')->each(function (Employee $employee) use ($addRow): void {
        $addRow('employee', $employee->slug, 'photo_path', $employee->photo_path);
    });

    NewsPost::query()->orderBy('id')->each(function (NewsPost $newsPost) use ($addRow): void {
        $addRow('news', $newsPost->slug, 'image_path', $newsPost->image_path);
    });

    GalleryAlbum::query()->orderBy('id')->each(function (GalleryAlbum $album) use ($addRow): void {
        $addRow('gallery_album', $album->slug, 'cover_image_path', $album->cover_image_path);
    });

    GalleryItem::query()->orderBy('id')->each(function (GalleryItem $item) use ($addRow): void {
        $identifier = $item->gallery_album_id . '#' . $item->id;
        $addRow('gallery_item', (string) $identifier, 'image_path', $item->image_path);
        $addRow('gallery_item', (string) $identifier, 'thumb_path', $item->thumb_path);
    });

    Property::query()->orderBy('id')->each(function (Property $property) use ($addRow): void {
        $coverPath = $property->images()->where('is_cover', true)->value('path');
        $addRow('property', $property->slug, 'cover_path', $coverPath);
    });

    PropertyImage::query()->orderBy('id')->each(function (PropertyImage $image) use ($addRow): void {
        $identifier = $image->property_id . '#' . $image->id;
        $addRow('property_image', (string) $identifier, 'path', $image->path);
        $addRow('property_image', (string) $identifier, 'thumb_path', $image->thumb_path);
    });

    $this->table(
        ['Type', 'Identifier', 'Field', 'Path', 'Status'],
        $rows,
    );

    if ($hasFailures) {
        $this->error('Media audit found missing or broken records. Review the table above.');

        return Command::FAILURE;
    }

    $this->info('Media audit passed.');

    return Command::SUCCESS;
})->purpose('Audit database media paths before production deployment');

Artisan::command(
    'realty:create-user
        {email : Login email}
        {password : User password}
        {name : Display name}
        {role=admin : admin or employee}
        {--employee= : Employee slug to link with the account}',
    function (string $email, string $password, string $name, string $role = 'admin'): int {
        $role = trim(strtolower($role));

        if (! in_array($role, ['admin', 'employee'], true)) {
            $this->error('Role must be either "admin" or "employee".');

            return Command::FAILURE;
        }

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => $role,
                'is_active' => true,
            ],
        );

        $employeeSlug = trim((string) $this->option('employee'));

        if ($employeeSlug !== '') {
            $employee = Employee::query()->where('slug', $employeeSlug)->first();

            if ($employee === null) {
                $this->error("Employee with slug [{$employeeSlug}] was not found.");

                return Command::FAILURE;
            }

            $employee->forceFill([
                'user_id' => $user->id,
                'is_admin' => $role === 'admin',
            ])->save();
        }

        $this->info('User has been created or updated successfully.');
        $this->line('Email: ' . $user->email);
        $this->line('Role: ' . $user->role);

        if ($employeeSlug !== '') {
            $this->line('Linked employee: ' . $employeeSlug);
        }

        return Command::SUCCESS;
    }
)->purpose('Create or update a production user and optionally link the account to an employee');

Artisan::command(
    'realty:backup-database
        {--output= : Output directory (default: storage/app/backups)}
        {--keep=7 : Number of backups to keep}
        {--force : Force run in production}',
    function (): int {
        if (app()->isProduction() && ! $this->option('force')) {
            $this->error('Use --force to run this command in production.');

            return Command::FAILURE;
        }

        $outputDir = $this->option('output') ?: storage_path('app/backups');
        $keepCount = (int) ($this->option('keep') ?: 7);

        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $connection = config('database.default');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$connection}_{$timestamp}.sql";
        $filepath = $outputDir . DIRECTORY_SEPARATOR . $filename;

        if ($connection === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');

            if (! file_exists($dbPath)) {
                $this->error("SQLite database not found: {$dbPath}");

                return Command::FAILURE;
            }

            copy($dbPath, $filepath);
            $this->info('SQLite database copied successfully.');
        } elseif (in_array($connection, ['mysql', 'mariadb'], true)) {
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            $port = $port ?: 3306;

            $cmd = sprintf(
                'mysqldump -h %s -P %d -u %s %s %s > %s 2>&1',
                escapeshellarg($host),
                (int) $port,
                escapeshellarg($username),
                $password ? '-p' . escapeshellarg($password) : '',
                escapeshellarg($database),
                escapeshellarg($filepath),
            );

            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0) {
                $this->error('MySQL backup failed. Ensure mysqldump is installed.');
                $this->line('Error: ' . implode("\n", $output));

                return Command::FAILURE;
            }

            $this->info('MySQL database dumped successfully.');
        } else {
            $this->error("Backup not supported for connection: {$connection}");

            return Command::FAILURE;
        }

        $size = file_exists($filepath) ? round(filesize($filepath) / 1024 / 1024, 2) : 0;
        $this->line("Backup file: {$filepath}");
        $this->line("Size: {$size} MB");

        // Cleanup old backups
        $pattern = $outputDir . DIRECTORY_SEPARATOR . 'backup_' . $connection . '_*.sql';
        $files = glob($pattern) ?: [];

        if (count($files) > $keepCount) {
            usort($files, fn (string $a, string $b): int => filemtime($b) <=> filemtime($a));

            foreach (array_slice($files, $keepCount) as $oldFile) {
                unlink($oldFile);
                $this->line("Removed old backup: {$oldFile}");
            }
        }

        $this->info("Backup completed. Kept last {$keepCount} backups.");

        return Command::SUCCESS;
    }
)->purpose('Create a database backup (SQLite copy or MySQL dump)');
