<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'photo_path',
        'quantity',
        'available_quantity',
        'description',
        'condition',
        'status',
    ];

    public function borrowings()
{
    return $this->hasMany(\App\Models\Borrowing::class);
}

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        return asset('storage/' . $this->photo_path);
    }
}
