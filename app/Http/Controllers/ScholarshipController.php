<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\User;

class ScholarshipController extends Controller
{
    // --------------------------------------------------
    // CENTRAL SCHOLARSHIP MANAGEMENT
    // --------------------------------------------------

    /**
     * List all scholarships with their conditions & requirements.
     */
    public function index()
    {
        $scholarships = Scholarship::with(['conditions', 'requirements'])->get();

        return view('central.scholarships.index', compact('scholarships'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('central.scholarships.create_scholarship');
    }

    /**
     * Store new scholarship
     */
    public function store(Request $request)
    {
        $request->validate([
            'scholarship_name' => 'required|string|max:255',
            'description'      => 'required|string',
            'deadline'         => 'required|date',
            'slots_available'  => 'nullable|integer',
            'grant_amount'     => 'nullable|numeric|min:0',
            'renewal_allowed'  => 'nullable|boolean',
        ]);

        $scholarship = Scholarship::create([
            'scholarship_name' => $request->scholarship_name,
            'description'      => $request->description,
            'deadline'         => $request->deadline,
            'slots_available'  => $request->slots_available,
            'grant_amount'     => $request->grant_amount,
            'renewal_allowed'  => $request->boolean('renewal_allowed'),
            'created_by'       => session('user_id'),
        ]);

        // Save conditions
        if ($request->has('conditions')) {
            foreach ($request->conditions as $cond) {
                $scholarship->conditions()->create([
                    'name' => $cond['type'],
                    'value' => $cond['value'],
                    'type' => 'condition',
                ]);
            }
        }

        // Save requirements
        if ($request->has('documents')) {
            foreach ($request->documents as $doc) {
                $scholarship->requirements()->create([
                    'name' => $doc['name'],
                    'type' => 'document',
                    'is_mandatory' => $doc['mandatory'] ?? true,
                ]);
            }
        }

        return redirect()
            ->route('central.dashboard')
            ->with('success', 'Scholarship added successfully.');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $scholarship = Scholarship::with(['conditions', 'requirements'])->findOrFail($id);

        return view('central.scholarships.create_scholarship', compact('scholarship'));
    }

    /**
     * Update scholarship
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'scholarship_name' => 'required|string|max:255',
            'description'      => 'required|string',
            'deadline'         => 'required|date',
            'slots_available'  => 'nullable|integer',
            'grant_amount'     => 'nullable|numeric|min:0',
            'renewal_allowed'  => 'nullable|boolean',
        ]);

        $scholarship = Scholarship::findOrFail($id);

        $scholarship->update([
            'scholarship_name' => $request->scholarship_name,
            'description'      => $request->description,
            'deadline'         => $request->deadline,
            'slots_available'  => $request->slots_available,
            'grant_amount'     => $request->grant_amount,
            'renewal_allowed'  => $request->boolean('renewal_allowed'),
        ]);

        // Refresh conditions
        $scholarship->conditions()->delete();
        if ($request->has('conditions')) {
            foreach ($request->conditions as $cond) {
                $scholarship->conditions()->create([
                    'name' => $cond['type'],
                    'value' => $cond['value'],
                    'type' => 'condition',
                ]);
            }
        }

        // Refresh requirements
        $scholarship->requirements()->delete();
        if ($request->has('documents')) {
            foreach ($request->documents as $doc) {
                $scholarship->requirements()->create([
                    'name' => $doc['name'],
                    'type' => 'document',
                    'is_mandatory' => $doc['mandatory'] ?? true,
                ]);
            }
        }

        return redirect()
            ->route('central.dashboard')
            ->with('success', 'Scholarship updated successfully.');
    }

    /**
     * Delete scholarship
     */
    public function destroy($id)
    {
        $scholarship = Scholarship::findOrFail($id);

        $scholarship->conditions()->delete();
        $scholarship->requirements()->delete();

        $scholarship->delete();

        return redirect()
            ->route('central.dashboard')
            ->with('success', 'Scholarship removed successfully.');
    }

    // --------------------------------------------------
    // SFAO SCHOLARSHIP MANAGEMENT
    // --------------------------------------------------

    /**
     * List all scholarships with applicant counts
     */
    public function sfaoIndex()
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        $sfaoCampus = $user->campus;
        $campusIds = $sfaoCampus->getAllCampusesUnder()->pluck('id');

        // Get scholarships with application counts filtered by campus
        $scholarships = Scholarship::withCount(['applications' => function($query) use ($campusIds) {
            $query->whereHas('user', function($userQuery) use ($campusIds) {
                $userQuery->whereIn('campus_id', $campusIds);
            });
        }])->get();

        return view('sfao.scholarships', compact('scholarships', 'sfaoCampus'));
    }

    /**
     * Show one scholarship with its applicants
     */
    public function sfaoShow($id)
    {
        if (!session()->has('user_id') || session('role') !== 'sfao') {
            return redirect('/login')->with('session_expired', true);
        }

        $user = User::with('campus')->find(session('user_id'));
        $sfaoCampus = $user->campus;
        $campusIds = $sfaoCampus->getAllCampusesUnder()->pluck('id');

        $scholarship = Scholarship::with(['applications' => function($query) use ($campusIds) {
            $query->whereHas('user', function($userQuery) use ($campusIds) {
                $userQuery->whereIn('campus_id', $campusIds);
            });
        }, 'applications.user'])->findOrFail($id);

        return view('sfao.scholarship_show', compact('scholarship', 'sfaoCampus'));
    }
}
