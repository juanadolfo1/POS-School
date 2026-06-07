<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradePromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_level_id',
        'from_grade',
        'to_grade',
        'is_final_grade',
    ];

    protected $casts = [
        'is_final_grade' => 'boolean',
    ];

    public function academicLevel()
    {
        return $this->belongsTo(CatAcademicLevel::class, 'academic_level_id');
    }
}
