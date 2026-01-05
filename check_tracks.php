<?php

use App\Models\Program;
use App\Models\ProgramTrack;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Program Tracks...\n";

$count = ProgramTrack::count();
echo "Total Tracks in DB: " . $count . "\n";

$programs = Program::with('tracks')->get();
$programsWithTracks = $programs->filter(fn($p) => $p->tracks->isNotEmpty());

echo "Programs with tracks: " . $programsWithTracks->count() . "\n";

foreach($programsWithTracks->take(5) as $p) {
    echo "Program: " . $p->name . "\n";
    foreach($p->tracks as $t) {
        echo " - " . $t->name . " (" . $t->type . ")\n";
    }
}
