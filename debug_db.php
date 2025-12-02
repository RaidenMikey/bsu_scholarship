<?php

use App\Models\Campus;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Campuses:\n";
$campuses = Campus::all();
foreach ($campuses as $campus) {
    echo "ID: {$campus->id}, Name: {$campus->name}\n";
}

echo "\nCampus-Department Pivot:\n";
$pivots = DB::table('campus_department')->get();
foreach ($pivots as $pivot) {
    echo "Campus ID: {$pivot->campus_id}, Dept ID: {$pivot->department_id}\n";
}

echo "\nDepartments:\n";
$depts = \App\Models\Department::all();
foreach ($depts as $dept) {
    echo "ID: {$dept->id}, Short Name: {$dept->short_name}\n";
}
