<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'reset_code',
        'reset_code_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'reset_code',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'reset_code_expires_at' => 'datetime', // ✅ THIS FIXES isPast() ERROR
        ];
    }
}
