<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'medicine_type',
        'quantity',
        'date_added',      
        'expiration_date',
        'description'
    ];

    protected $casts = [
    'date_added' => 'date',
    'expiration_date' => 'date',
];
}
