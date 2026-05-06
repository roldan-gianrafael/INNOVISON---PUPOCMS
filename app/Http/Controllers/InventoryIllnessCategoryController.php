<?php

namespace App\Http\Controllers;

use App\Models\InventoryIllnessCategory;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryIllnessCategoryController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('Y-m'));
        $categories = InventoryIllnessCategory::query()
            ->with(['items' => function ($query) {
                $query->orderBy('name');
            }])
            ->withCount('items')
            ->orderBy('name')
            ->get();

        return view('admin.reports.manage-inventory-illness-categories', compact('categories', 'month'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $normalizedName = mb_strtolower(trim((string) $request->name));

        $duplicateExists = InventoryIllnessCategory::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalizedName])
            ->exists();

        if ($duplicateExists) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'name' => 'This inventory illness category already exists.',
                ]);
        }

        InventoryIllnessCategory::create([
            'name' => trim((string) $request->name),
        ]);

        return redirect()->back()->with('success', 'New inventory illness category added.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $category = InventoryIllnessCategory::findOrFail($id);
        $normalizedName = mb_strtolower(trim((string) $request->name));

        $duplicateExists = InventoryIllnessCategory::query()
            ->where('id', '!=', $category->id)
            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalizedName])
            ->exists();

        if ($duplicateExists) {
            return redirect()
                ->back()
                ->withErrors([
                    'name' => 'This inventory illness category already exists.',
                ]);
        }

        $category->update([
            'name' => trim((string) $request->name),
        ]);

        return redirect()->back()->with('success', 'Inventory illness category updated successfully.');
    }

    public function destroy($id)
    {
        $category = InventoryIllnessCategory::findOrFail($id);

        DB::transaction(function () use ($category) {
            Item::query()
                ->where('illness_category_id', $category->id)
                ->update(['illness_category_id' => null]);

            $category->delete();
        });

        return redirect()->back()->with('success', 'Inventory illness category removed successfully.');
    }
}
