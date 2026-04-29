<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentTutor extends Model
{
    protected $fillable = [
        'status',
        'tutor_type',
        'relation',
        'person_id',
        'student_id'
    ];

    use HasFactory, SoftDeletes;
}
