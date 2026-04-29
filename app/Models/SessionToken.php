<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionToken extends Model
{
    protected $fillable = [
        'token',
        'expiration',
        'user_id'
    ];

    use HasFactory, SoftDeletes;
}
