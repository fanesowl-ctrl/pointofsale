<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Kasir extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'kasir'; // Tabel khusus kasir

    protected $fillable = [
        'name',
        'username',
        'password',
    ];

    protected $hidden = [
        // 'password', // Show password
    ];
}
