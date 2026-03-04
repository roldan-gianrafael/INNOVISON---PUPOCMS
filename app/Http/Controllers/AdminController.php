<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Item;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response; // <-- for CSV exports
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // ==========================================
    //  PART 1: VIEW METHODS (Loading the Pages)
    // ==========================================

    public function dashboard()
    {
        $total = Appointment::count();
        $pending = Appointment::where('status', 'Pending')->count();
        $upcoming = Appointment::where('status', 'Approved')->count();
        $completed = Appointment::where('status', 'Completed')->count();
        $cancelled = Appointment::where('status', 'Cancelled')->count();

        $currentYear = now()->year;
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $monthlyTotalsRaw = Appointment::selectRaw('MONTH(date) as month_num, COUNT(*) as total')
            ->whereYear('date', $currentYear)
            ->groupBy('month_num')
            ->pluck('total', 'month_num');

        $monthlyCompletedRaw = Appointment::selectRaw('MONTH(date) as month_num, COUNT(*) as total')
            ->whereYear('date', $currentYear)
            ->where('status', 'Completed')
            ->groupBy('month_num')
            ->pluck('total', 'month_num');

        $monthlyTotals = [];
        $monthlyCompleted = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyTotals[] = (int) ($monthlyTotalsRaw[$month] ?? 0);
            $monthlyCompleted[] = (int) ($monthlyCompletedRaw[$month] ?? 0);
        }

        $recentAppointments = Appointment::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'total',
            'pending',
            'upcoming',
            'completed',
            'cancelled',
            'recentAppointments',
            'currentYear',
            'monthLabels',
            'monthlyTotals',
            'monthlyCompleted'
        ));
    }

    public function appointments()
    {
        $appointments = Appointment::latest()->get();
        return view('admin.appointments', compact('appointments'));
    }

    public function inventory()
    {
        $items = Item::all();
        return view('admin.inventory', compact('items'));
    }

    public function reports()
{
   
    $appointments = Appointment::all();
    $total = Appointment::count();
    $approved = Appointment::where('status', 'Approved')->count();
    $cancelled = Appointment::where('status', 'Cancelled')->count();
    

    $lowStockCount = Item::where('quantity', '<', 10)->count(); 
    
   
    $appointmentsToday = Appointment::where('status', 'Approved')
                                    ->whereDate('date', \Carbon\Carbon::today())
                                    ->count();


    $totalConsultations = Appointment::where('status', 'Approved')
                                     ->whereMonth('date', \Carbon\Carbon::now()->month)
                                     ->count();

    $items = Item::all();

    return view('admin.reports', compact(
        'appointments', 'total', 'approved', 'cancelled', 
        'lowStockCount', 'appointmentsToday', 'totalConsultations', 'items'
    ));
}

    public function settings()
    {
        $admin = Auth::user();
        $settings = Setting::first();
        if(!$settings) { $settings = new Setting(); }
        return view('admin.settings', compact('admin', 'settings'));
    }

    // ==========================================
    //  PART 2: ACTION METHODS (The Real Logic)
    // ==========================================

    // --- 1. APPOINTMENT ACTIONS ---
    public function updateStatus($id, $status)
    {
        $appointment = Appointment::find($id);
        if ($appointment) {
            $appointment->status = $status;
            $appointment->save();
            return redirect()->back()->with('success', "Appointment marked as $status.");
        }
        return redirect()->back()->with('error', "Appointment not found.");
    }

    public function reschedule($id, Request $request)
    {
        $appointment = Appointment::find($id);
        if ($appointment) {
            $appointment->date = $request->date;
            $appointment->time = $request->time;
            $appointment->status = 'Approved';
            $appointment->save();
            return redirect()->back()->with('success', "Appointment rescheduled successfully.");
        }
        return redirect()->back()->with('error', "Error rescheduling.");
    }

    // --- 2. INVENTORY ACTIONS ---
    public function storeItem(Request $request)
    {
        Item::create($request->all());
        return redirect()->back()->with('success', 'New item added to inventory.');
    }

    public function updateItem($id, Request $request)
    {
        $item = Item::find($id);
        if ($item) {
            $item->update($request->all());
            return redirect()->back()->with('success', 'Item updated successfully.');
        }
        return redirect()->back()->with('error', 'Item not found.');
    }

    public function deleteItem($id)
    {
        Item::destroy($id);
        return redirect()->back()->with('success', 'Item removed.');
    }

    // --- 3. SETTINGS & PROFILE ---
    public function updateSettings(Request $request)
    {
        $settings = Setting::first();
        if(!$settings) { $settings = new Setting(); }

        $settings->clinic_name = $request->clinic_name;
        $settings->clinic_location = $request->clinic_location;
        $settings->open_time = $request->open_time;
        $settings->close_time = $request->close_time;
        $settings->email_notifications = $request->has('email_notifications');
        $settings->auto_approve = $request->has('auto_approve');
        $settings->save();

        return redirect()->back()->with('success', 'System settings saved.');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    // --- 4. EXPORTS (CSV) ---
    public function exportReports()
    {
        $appointments = Appointment::all();
        $filename = "appointments_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = ["Content-Type" => "text/csv", "Content-Disposition" => "attachment; filename={$filename}"];
        $columns = ['ID','Name','Email','Student ID','Service','Date','Time','Status','Notes'];

        $callback = function() use ($appointments, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($appointments as $appt) {
                fputcsv($file, [
                    $appt->id,
                    $appt->name,
                    $appt->email,
                    $appt->student_id,
                    $appt->service,
                    $appt->date,
                    $appt->time,
                    $appt->status,
                    $appt->notes
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportInventory()
    {
        $items = Item::all();
        $filename = "inventory_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = ["Content-Type" => "text/csv", "Content-Disposition" => "attachment; filename={$filename}"];
        $columns = ['ID','Name','Category','Quantity'];

        $callback = function() use ($items, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->name,
                    $item->category,
                    $item->quantity
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // --- 5. COMPLETE APPOINTMENT & DEDUCT INVENTORY ---
    public function completeWithMedicine(Request $request, $id)
    {
        $appointment = Appointment::find($id);
        if(!$appointment) return redirect()->back()->with('error', 'Appointment not found.');

        $appointment->status = 'Completed';
        $appointment->save();

        if ($request->filled('item_id')) {
            $item = Item::find($request->item_id);
            if ($item && $item->quantity > 0) {
                $item->decrement('quantity', 1);
                return redirect()->back()->with('success', "Appointment completed and 1 {$item->name} deducted.");
            } elseif ($item) {
                return redirect()->back()->with('error', "Appointment completed, but {$item->name} is OUT OF STOCK.");
            }
        }

        return redirect()->back()->with('success', 'Appointment completed (No medicine deducted).');
    }

    // 6. FOR INVENTORY SUMMARY
    public function inventorySummary()
{
    // Kuhanin ang breakdown ng inventory
    $totalItems = Item::count();
    $totalStock = Item::sum('quantity');
    $outOfStock = Item::where('quantity', 0)->count();
    $lowStockItems = Item::where('quantity', '>', 0)->where('quantity', '<', 10)->get();
    
    // Grouping by category para sa summary table
    $categorySummary = Item::select('category', 
                    DB::raw('count(*) as count'), // May backslash na sa unahan
                    DB::raw('sum(quantity) as total_qty'))
                    ->groupBy('category')
                    ->get();

    return view('admin.reports.inventory-summary', compact(
        'totalItems', 'totalStock', 'outOfStock', 'lowStockItems', 'categorySummary'
    ));
}
}
