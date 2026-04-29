<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatAcademicLevel extends Model
{
    protected $fillable = [
        'label',
        'status'
    ];

    use HasFactory, SoftDeletes;
}
