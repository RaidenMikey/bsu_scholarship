<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Scholarship; // ← add this
use Illuminate\Http\Request;

class CentralController extends Controller
{
    public function dashboard()
    {
        $applications = Application::with(['user', 'scholarship'])
            ->orderBy('created_at', 'desc')
            ->get();

        $scholarships = Scholarship::orderBy('created_at', 'desc')->get(); // ← fetch scholarships

        return view('central.dashboard', compact('applications', 'scholarships')); // ← pass both
    }
}
