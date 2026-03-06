<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthProfile extends Model
{
    use HasFactory;

    // 1. Dito natin inililista kung anong columns ang pwedeng lagyan ng data
    protected $fillable = [
        'user_id', 
        'emergency_contact_name', 
        'emergency_contact_number', 
        'blood_type', 
        'medical_history', // Dito papasok yung mga checkboxes
        'is_smoker', 
        'is_drinker', 
        'vaccine_history'  // Dito papasok yung vaccine table data
    ];

    // 2. Importante 'to: Ginagawa nitong "Array" yung JSON galing sa database
    // para hindi mag-error kapag tinawag mo sa Blade or Controller.
    protected $casts = [
        'medical_history' => 'array',
        'vaccine_history' => 'array',
    ];

    // 3. Relationship: Ikinakabit natin ang profile na ito sa isang User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}