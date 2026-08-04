<?php
require '/var/www/vendor/autoload.php';
$app = require '/var/www/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Total: " . App\Models\Property::count() . "\n";
echo "Published: " . App\Models\Property::where('is_published', true)->count() . "\n\n";

foreach (App\Models\Property::all() as $p) {
    echo "{$p->id} | city={$p->city} | deal={$p->deal_type} | type={$p->property_type} | rooms={$p->rooms} | price={$p->price} | featured=" . ($p->is_featured ? 'yes' : 'no') . " | published=" . ($p->is_published ? 'yes' : 'no') . "\n";
}