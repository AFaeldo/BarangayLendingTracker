<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'alias',
        'gender',
        'marital_status',
        'spouse_name',
        'birthdate',
        'place_of_birth',
        'age',
        'age_month',
        'height_cm',
        'weight_kg',
        'sitio',
        'purok',
        'contact',
        'employment_status',
        'religion',
        'voter_status',
        'is_pwd',
        'status',
        'remarks',
    ];

    public function borrowings()
    {
        return $this->hasMany(\App\Models\Borrowing::class);
    }

    // Accessor for middle initial
    public function getMiddleInitialAttribute()
    {
        return $this->middle_name ? strtoupper(substr($this->middle_name, 0, 1)) : '';
    }
}
