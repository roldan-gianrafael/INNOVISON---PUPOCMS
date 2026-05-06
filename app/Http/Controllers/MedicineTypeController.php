<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\MedicineType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineTypeController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('Y-m'));
        $medicineTypes = MedicineType::query()
            ->with(['items' => function ($query) {
                $query->orderBy('name');
            }])
            ->withCount('items')
            ->orderBy('name')
            ->get();

        return view('admin.reports.manage-medicine-types', compact('medicineTypes', 'month'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $normalizedName = mb_strtolower(trim((string) $request->name));
        $duplicateExists = MedicineType::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalizedName])
            ->exists();

        if ($duplicateExists) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'name' => 'This medicine type already exists.',
                ]);
        }

        MedicineType::create([
            'name' => trim((string) $request->name),
        ]);

        return redirect()->back()->with('success', 'New medicine type added.');
    }

    public function destroy($id)
    {
        $medicineType = MedicineType::findOrFail($id);

        DB::transaction(function () use ($medicineType) {
            Item::query()
                ->where('medicine_type_id', $medicineType->id)
                ->update([
                    'medicine_type_id' => null,
                    'medicine_type' => null,
                ]);

            $medicineType->delete();
        });

        return redirect()->back()->with('success', 'Medicine type removed successfully.');
    }
}
