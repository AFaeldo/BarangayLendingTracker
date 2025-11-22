<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'item_id',
        'quantity',
        'date_borrowed',
        'due_date',
        'returned_at',
        'status',
        'remarks',
        'is_lost',            // 👈 NEW
        'condition_returned', // 👈 NEW
        'received_by',        // 👈 NEW
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
