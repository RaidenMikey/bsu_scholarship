<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScholarshipCondition;

class ScholarshipConditionController extends Controller
{
    /**
     * Store a new scholarship condition.
     */
    public function store(Request $request, $scholarshipId)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'value'      => 'required|string|max:255',
        ]);

        ScholarshipCondition::create([
            'scholarship_id' => $scholarshipId,
            'field_name'     => $validated['field_name'],
            'value'          => $validated['value'],
        ]);

        return redirect()->back()->with('success', 'Condition added successfully.');
    }

    /**
     * Delete a scholarship condition.
     */
    public function destroy($id)
    {
        $condition = ScholarshipCondition::findOrFail($id);
        $condition->delete();

        return redirect()->back()->with('success', 'Condition removed successfully.');
    }
}
