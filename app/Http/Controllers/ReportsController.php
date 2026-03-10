<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MedicalConditions;
use App\Models\Category;
use App\Models\Consultation;

class ReportsController extends Controller
{
    public function marReport(Request $request)
{
    $monthFilter = $request->input('month', date('Y-m')); 
    $year = date('Y', strtotime($monthFilter));
    $month = date('m', strtotime($monthFilter));

    // Gamitin ang consultations table columns directly para sa counting
    $categories = Category::with(['medicalConditions.consultations' => function($query) use ($year, $month) {
        $query->whereYear('consultation_date', $year)
              ->whereMonth('consultation_date', $month);
    }])->get();

    $allConditions = MedicalConditions::with('category')->get();
    $categoryList = Category::all();
    $totalToday = Consultation::whereDate('consultation_date', today())->count();

    return view('admin.reports.mar', [
        'categories' => $categories,
        'allConditions' => $allConditions,
        'categoryList' => $categoryList,
        'month' => $monthFilter,
        'totalToday' => $totalToday
    ]);
}
    // for managing mar
    public function manageMar(Request $request)
{
    $month = $request->get('month', date('Y-m'));
    
    // Para sa dropdown ng categories
    $categoryList = Category::all(); 
    
    // Para sa listahan ng conditions sa table
    $allConditions = MedicalConditions::with('category')->get(); 

    // Iba pang logic para sa reports...
    $categories = Category::with(['medicalConditions.consultations' => function($query) use ($month) {
        $query->where('consultation_date', 'like', $month . '%');
    }])->get();

    return view('admin.reports.manage-mar', compact('categoryList', 'allConditions', 'categories', 'month'));
}

//for changing category
public function update(Request $request, $id)
{
    $request->validate(['category_id' => 'required|exists:categories,id']);
    
    $condition = MedicalConditions::findOrFail($id);
    $condition->update([
        'category_id' => $request->category_id
    ]);

    return back()->with('success', 'Category updated successfully!');
}
// Para sa Export Hub Landing Page
public function exportHub() 
{
    return view('admin.reports.export-reports'); 
}

// Para sa Universal Printing System
public function printReport(Request $request)
{
    $type = $request->query('type'); // mar, inventory, or appointment
    $monthFilter = $request->input('month', date('Y-m'));
    $year = date('Y', strtotime($monthFilter));
    $month = date('m', strtotime($monthFilter));

    $title = "";
    $data = [];

    if ($type == 'mar') {
        $title = "MONTHLY ACCOMPLISHMENT REPORT";
        // for categories
        $data = \App\Models\Category::with(['medicalConditions.consultations' => function($query) use ($year, $month) {
            $query->whereYear('consultation_date', $year)
                  ->whereMonth('consultation_date', $month);
        }])->get();
    } 
    elseif ($type == 'inventory') {
    $title = "INVENTORY STOCK REPORT";
    
    // Kunin ang lahat ng items sa table na 'items'
    $data = \App\Models\Item::all(); 

    // Dahil wala kang 'consumed' column sa DB, i-inject natin ito bilang temporary data
    $data->transform(function($item) {
        $item->consumed = 0; // Placeholder muna dahil walang history sa table
        $item->starting = $item->quantity; // I-assume natin na ito ang starting
        return $item;
    });
}
    elseif ($type == 'appointment') {
    $title = "APPOINTMENT SUMMARY REPORT";
    // for date
    $data = \App\Models\Appointment::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();
}

    // Siguraduhin na ang view name ay tumutugma sa ginawa mong file
    return view('admin.reports.print-reports', compact('data', 'type', 'title', 'monthFilter'));
}
}
