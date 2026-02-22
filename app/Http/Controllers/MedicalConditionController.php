<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalConditions; // Siguraduhing tugma sa model name mo

class MedicalConditionController extends Controller
{
    /**
     * Store a newly created medical condition.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        MedicalConditions::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
        ]);

        return redirect()->back()->with('success', 'New condition added to MAR!');
    }

    /**
     * Remove the specified medical condition (Soft Delete).
     */
    public function destroy($id)
    {
        $condition = MedicalConditions::findOrFail($id);
        $condition->delete();

        return redirect()->back()->with('success', 'Condition removed successfully.');
    }
}