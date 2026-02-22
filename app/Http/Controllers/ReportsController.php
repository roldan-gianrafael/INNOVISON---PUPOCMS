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
        // 1. Filter Logic
        $monthFilter = $request->input('month', date('Y-m')); 
        $year = date('Y', strtotime($monthFilter));
        $month = date('m', strtotime($monthFilter));

        // 2. MAR Statistical Data (Categories A-E)
        // Kunin ang Categories (A-E) kasama ang conditions at counts ng consultations base sa role ng user
        $categories = Category::with(['medicalConditions.consultations' => function($query) use ($year, $month) {
            $query->whereYear('consultation_date', $year)
                  ->whereMonth('consultation_date', $month)
                  ->with('user'); // Para makuha natin ang role (Student/Faculty/Admin)
        }])->get();

        // 3. Data for "Manage MAR" section (CRUD part sa iisang blade)
        $allConditions = MedicalConditions::with('category')->get();
        $categoryList = Category::all(); // Para sa dropdown sa pag-add ng bagong sakit

        // 4. Totals for the Dashboard Widget (Today Only)
        $totalToday = Consultation::whereDate('consultation_date', today())->count();

        return view('admin.reports.mar', [
            'categories' => $categories,
            'allConditions' => $allConditions,
            'categoryList' => $categoryList,
            'month' => $monthFilter,
            'totalToday' => $totalToday
        ]);
    }

    // for managinf mar
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

    // Export to Excel / CSV
    public function exportExcel(Request $request)
    {
        $monthFilter = $request->get('month', date('Y-m'));
        $year = date('Y', strtotime($monthFilter));
        $month = date('m', strtotime($monthFilter));

        $categories = Category::with(['medicalConditions.consultations' => function($query) use ($year, $month) {
            $query->whereYear('consultation_date', $year)
                  ->whereMonth('consultation_date', $month)
                  ->with('user');
        }])->get();

        $filename = 'MAR_Report_' . $monthFilter . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Category/Condition', 'Student', 'Faculty', 'Admin', 'Total']);

        foreach ($categories as $cat) {
            fputcsv($handle, [$cat->code . ' - ' . $cat->name, '', '', '', '']); // Header ng Category
            
            foreach ($cat->medicalConditions as $condition) {
                $stu = $condition->consultations->where('user.role', 'Student')->count();
                $fac = $condition->consultations->where('user.role', 'Faculty')->count();
                $adm = $condition->consultations->where('user.role', 'Admin')->count();
                
                fputcsv($handle, [
                    '  ' . $condition->name,
                    $stu,
                    $fac,
                    $adm,
                    ($stu + $fac + $adm)
                ]);
            }
        }

        fclose($handle);
        exit;
    }
}