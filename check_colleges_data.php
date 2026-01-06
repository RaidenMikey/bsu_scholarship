<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\College;

echo "\n--- Standard Colleges ---\n";
$colleges = College::all();
foreach($colleges as $c) {
    echo "ID: {$c->id}, Name: {$c->name}, Short: {$c->short_name}\n";
}

echo "\n--- Distinct User Colleges (Student Role) ---\n";
$userColleges = User::where('role', 'student')->select('college')->distinct()->pluck('college');
foreach($userColleges as $uc) {
    echo "'$uc'\n";
}
