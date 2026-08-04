<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::query()->where('email', 'shlyakhov@realty-tuapse.local')->first();
if ($user === null) {
    echo "USER NOT FOUND\n";
    exit(1);
}

$user->password = \Illuminate\Support\Facades\Hash::make('ChangeMe2026!');
$user->save();

echo 'reset done. verify: ' . (\Illuminate\Support\Facades\Hash::check('ChangeMe2026!', $user->fresh()->password) ? 'OK' : 'FAIL') . PHP_EOL;
