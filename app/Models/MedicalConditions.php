<?php

// app/Models/MedicalCondition.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalConditions extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'category_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function consultations(): HasMany
    {
       return $this->hasMany(Consultation::class, 'medical_condition_id');
    }
}