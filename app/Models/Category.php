<?php

// app/Models/Category.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    // Idinagdag ang 'code' para sa A, B, C, D, E muna
    protected $fillable = ['code', 'name'];

    /**
     * Relationship: Ito ay Medical Conditions (Sub-categories)
     */
    public function medicalConditions(): HasMany
    {
        return $this->hasMany(MedicalConditions::class);
    }

    /**
     * Helper logic para sa MAR Report: 
     * Kinukuha ang lahat ng consultations sa ilalim ng category na ito sa pamamagitan ng mga conditions.
     */
    public function consultations()
    {
        return $this->hasManyThrough(Consultation::class, MedicalConditions::class);
    }
}