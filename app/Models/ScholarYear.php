<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScholarYear extends Model
{
    protected $fillable = [
        'status',
        'year',
        'starts_at',
        'ends_at'
    ];

    use HasFactory, SoftDeletes;
}
