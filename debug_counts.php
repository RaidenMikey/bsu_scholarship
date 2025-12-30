<?php

use App\Models\User;
use App\Models\Campus;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$campus = Campus::where('name', 'LIKE', '%ARASOF%')->first();
if (!$campus) {
    echo "ARASOF campus not found. Listing all:\n";
    Campus::all()->each(function($c) { echo $c->name . "\n"; });
    exit;
}

echo "Analyzing Data for Campus: {$campus->name}\n\n";

// 1. Count used in 'Applicants List' (Excludes Scholars)
$listCount = User::where('role', 'student')
    ->where('campus_id', $campus->id)
    ->whereDoesntHave('scholars')
    ->whereHas('applications')
    ->count();

echo "Count in 'Applicants List' (Unique Students, Non-Scholars): {$listCount}\n";

// 2. Count used in 'Report' (Includes Scholars if they have applications)
$reportStudents = User::where('role', 'student')
    ->where('campus_id', $campus->id)
    ->whereHas('applications')
    ->with(['applications.scholarship'])
    ->get();

$reportUniqueStudentsCount = $reportStudents->count();
$totalApplicationsCount = 0;

$studentsWithMultiApps = [];
$scholarsWithApps = [];

foreach ($reportStudents as $student) {
    $appCount = $student->applications->count();
    $totalApplicationsCount += $appCount;

    if ($appCount > 1) {
        $names = $student->applications->map(fn($a) => $a->scholarship->scholarship_name ?? 'Unknown')->implode(', ');
        $studentsWithMultiApps[] = "{$student->name} (Apps: {$appCount}) - [{$names}]";
    }

    if ($student->scholars()->exists()) {
        $scholarsWithApps[] = "{$student->name} (is also a Scholar)";
    }
}

echo "Unique Students in Report: {$reportUniqueStudentsCount}\n";
echo "Total Rows in Report (Applications): {$totalApplicationsCount}\n\n";

if (count($studentsWithMultiApps) > 0) {
    echo "--- Students with Multiple Applications ---\n";
    foreach ($studentsWithMultiApps as $info) echo $info . "\n";
    echo "\n";
} else {
    echo "No students with multiple applications found.\n\n";
}

if (count($scholarsWithApps) > 0) {
    echo "--- Students in Report who are actually Scholars (Hidden from Applicant List) ---\n";
    foreach ($scholarsWithApps as $info) echo $info . "\n";
    echo "\n";
} else {
    echo "No scholars found in the applicant report.\n";
}
