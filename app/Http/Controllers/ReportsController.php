<?php
// app/Http/Controllers/ReportsController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MedicalCondition;
use App\Models\Category;

class ReportsController extends Controller
{
    // MAR Report view
   public function marReport(Request $request)
{
    $selectedMonth = $request->get('month');

    // Fetch raw data
    $queryResults = DB::table('consultations')
        ->join('medical_conditions', 'consultations.medical_condition_id', '=', 'medical_conditions.id')
        ->join('categories', 'medical_conditions.category_id', '=', 'categories.id')
        ->join('users', 'consultations.user_id', '=', 'users.id')
        ->select(
            'categories.name as c_name',
            'users.role as u_role',
            DB::raw('COUNT(consultations.id) as total_count')
        )
        ->where('users.role', 'Student')
        ->groupBy('categories.name', 'users.role')
        ->get();

    $finalReport = [];

    foreach ($queryResults as $row) {
        // FIX: Force the category name to be a string. 
        // Using (string) or strval() prevents "Illegal offset type"
        $categoryKey = (string)($row->c_name ?? 'Uncategorized');

        if (!isset($finalReport[$categoryKey])) {
            $finalReport[$categoryKey] = [
                'Student' => 0,
                'Total'   => 0
            ];
        }

        // Fill the data
        $finalReport[$categoryKey]['Student'] = (int)$row->total_count;
        $finalReport[$categoryKey]['Total']   = (int)$row->total_count;
    }

    return view('admin.reports.mar', [
        'report' => $finalReport, 
        'month'  => $selectedMonth
    ]);
}

    // Export to Excel (optional)
    public function exportExcel(Request $request)
    {
        $month = $request->get('month');
        $query = DB::table('consultations')
            ->join('medical_conditions', 'consultations.medical_condition_id', '=', 'medical_conditions.id')
            ->join('categories', 'medical_conditions.category_id', '=', 'categories.id')
            ->join('users', 'consultations.user_id', '=', 'users.id')
            ->select(
                'categories.name as category_name',
                'users.role as patient_type',
                DB::raw('COUNT(consultations.id) as total')
            )
            ->groupBy('categories.name', 'users.role');

        if ($month) $query->whereMonth('consultation_date', $month);

        $reportRaw = $query->get();

        $report = [];
        foreach ($reportRaw as $row) {
            $category = $row->category_name;
            $role = $row->patient_type;

            if (!isset($report[$category])) {
                $report[$category] = [
                    'Student' => 0,
                    'Faculty' => 0,
                    'Admin' => 0,
                    'Total' => 0
                ];
            }

            $report[$category][$role] = $row->total;
            $report[$category]['Total'] += $row->total;
        }

        // Export using Laravel Excel (optional)
        // For simplicity, return CSV
        $filename = 'MAR_Report_' . now()->format('Ymd_His') . '.csv';
        $handle = fopen($filename, 'w');
        fputcsv($handle, ['Category', 'Student', 'Faculty', 'Admin', 'Total']);

        foreach ($report as $category => $data) {
            fputcsv($handle, [
                $category,
                $data['Student'],
                $data['Faculty'],
                $data['Admin'],
                $data['Total']
            ]);
        }

        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }
}