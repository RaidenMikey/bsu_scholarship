<?php
use App\Models\User;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$email = '22-75021@g.batstate-u.edu.ph';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found.\n";
    exit;
}

echo "User ID: " . $user->id . "\n";

$applications = Application::where('user_id', $user->id)->get();

if ($applications->isEmpty()) {
    echo "No active applications found.\n";
} else {
    echo "Found " . $applications->count() . " applications.\n";
    foreach ($applications as $app) {
        echo " - App ID: " . $app->id . ", Scholarship ID: " . $app->scholarship_id . ", Status: " . $app->status . "\n";
        // Delete it to reset state
        $app->delete();
        echo "   Deleted application to reset state.\n";
    }
}
