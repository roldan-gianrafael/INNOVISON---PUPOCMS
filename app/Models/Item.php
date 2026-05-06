<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'medicine_type_id',
        'medicine_type',
        'illness_category_id',
        'quantity',
        'unit',
        'dispensing_unit',
        'units_per_stock_unit',
        'date_added',      
        'expiration_date',
        'description'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'units_per_stock_unit' => 'integer',
        'date_added' => 'date',
        'expiration_date' => 'date',
    ];

    public function illnessCategory(): BelongsTo
    {
        return $this->belongsTo(InventoryIllnessCategory::class, 'illness_category_id');
    }

    public function medicineType(): BelongsTo
    {
        return $this->belongsTo(MedicineType::class, 'medicine_type_id');
    }

    public function normalizedUnit(): string
    {
        return Str::lower(trim((string) ($this->unit ?: 'pcs')));
    }

    public function normalizedDispensingUnit(): string
    {
        return Str::lower(trim((string) ($this->dispensing_unit ?: '')));
    }

    public function unitsPerStockUnit(): int
    {
        return max(1, (int) ($this->units_per_stock_unit ?: 1));
    }

    public function hasDispensingConversion(): bool
    {
        return $this->normalizedDispensingUnit() !== ''
            && $this->unitsPerStockUnit() > 1;
    }

    public function requiresDispensingConversion(): bool
    {
        return in_array($this->normalizedUnit(), [
            'box',
            'boxes',
            'pack',
            'packs',
            'bottle',
            'vial',
            'ampule',
            'ampoule',
            'tube',
            'sachet',
        ], true);
    }

    public function availableDispensingQuantity(): float
    {
        $stockQuantity = (float) $this->quantity;

        if ($this->hasDispensingConversion()) {
            return $stockQuantity * $this->unitsPerStockUnit();
        }

        return $stockQuantity;
    }

    public function convertDispensingQuantityToStockQuantity(float $dispensingQuantity): float
    {
        if ($this->hasDispensingConversion()) {
            return $dispensingQuantity / $this->unitsPerStockUnit();
        }

        return $dispensingQuantity;
    }
}
