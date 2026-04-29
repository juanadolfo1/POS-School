<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scholarship extends Model
{
    protected $fillable = [
        'status',
        'name',
        'percentage',
        'student_id',
        'scholar_year_id'
    ];

    use HasFactory, SoftDeletes;
}
