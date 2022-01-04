<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetRecord extends Model
{
    protected $fillable = [
        'email',
        'token'
    ];
}
