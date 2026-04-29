<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    protected $fillable = [
        'status',
        'label',
        'scholar_year_id',
        'academic_level_id'
    ];

    use HasFactory, SoftDeletes;
}
