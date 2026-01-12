<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Item;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // ==========================================
    //  PART 1: VIEW METHODS (Loading the Pages)
    // ==========================================

    public function dashboard()
    {
        // 1. Fetch Real Stats
        $total = Appointment::count();
        $pending = Appointment::where('status', 'Pending')->count();
        $upcoming = Appointment::where('status', 'Approved')->count();
        $completed = Appointment::where('status', 'Completed')->count();
        $cancelled = Appointment::where('status', 'Cancelled')->count();
        
        // 2. Fetch Recent Activity
        $recentAppointments = Appointment::latest()->take(5)->get();

        return view('admin.dashboard', compact('total', 'pending', 'upcoming', 'completed', 'cancelled', 'recentAppointments'));
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
        $lowStock = Item::where('quantity', '<', 10)->count();
        $items = Item::all();

        return view('admin.reports', compact('appointments', 'total', 'approved', 'cancelled', 'lowStock', 'items'));
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

    // --- 1. APPOINTMENT ACTIONS (FIXED) ---
    
    // This handles Approve / Cancel / Complete logic
    public function updateStatus($id, $status)
    {
        // 1. Find the appointment in the database
        $appointment = Appointment::find($id);
        
        // 2. If it exists, update the status and SAVE it
        if ($appointment) {
            $appointment->status = $status;
            $appointment->save(); // <--- This updates the database!
            
            // 3. Redirect back (No more white screen)
            return redirect()->back()->with('success', "Appointment marked as $status.");
        }

        return redirect()->back()->with('error', "Appointment not found.");
    }

    // This handles Rescheduling
    public function reschedule($id, Request $request)
    {
        $appointment = Appointment::find($id);
        
        if ($appointment) {
            $appointment->date = $request->date;
            $appointment->time = $request->time;
            $appointment->status = 'Approved'; // Auto-approve if rescheduled by admin
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
        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        
        $user->save();
        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    // --- 4. EXPORTS ---
    public function exportReports() { return redirect()->back()->with('success', 'Export function coming soon!'); }
    public function exportInventory() { return redirect()->back()->with('success', 'Export function coming soon!'); }

    // New function to Complete Appointment & Deduct Inventory
    // USAGE: You will need a form in your Admin UI that sends 'item_id' to this route
    public function completeWithMedicine(Request $request, $id)
    {
        $appointment = Appointment::find($id);
        
        if(!$appointment) {
            return redirect()->back()->with('error', 'Appointment not found.');
        }

        // 1. Mark as Completed
        $appointment->status = 'Completed';
        $appointment->save();

        // 2. Deduct Medicine (If selected)
        if ($request->filled('item_id')) {
            $item = Item::find($request->item_id);
            
            if ($item && $item->quantity > 0) {
                $item->decrement('quantity', 1); // Deduct 1 from stock
                return redirect()->back()->with('success', "Appointment completed and 1 {$item->name} deducted.");
            } elseif ($item && $item->quantity <= 0) {
                return redirect()->back()->with('error', "Appointment completed, but {$item->name} is OUT OF STOCK.");
            }
        }

        return redirect()->back()->with('success', 'Appointment completed (No medicine deducted).');
    }
}