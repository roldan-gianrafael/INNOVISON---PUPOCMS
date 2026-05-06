<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicineType extends Model
{
    protected $fillable = [
        'name',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'medicine_type_id');
    }
}
