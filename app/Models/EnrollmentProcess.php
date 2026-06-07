<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_scholar_year_id',
        'to_scholar_year_id',
        'academic_level_id',
        'students_promoted',
        'students_graduated',
        'students_excluded',
    ];

    public function fromScholarYear()
    {
        return $this->belongsTo(ScholarYear::class, 'from_scholar_year_id');
    }

    public function toScholarYear()
    {
        return $this->belongsTo(ScholarYear::class, 'to_scholar_year_id');
    }

    public function academicLevel()
    {
        return $this->belongsTo(CatAcademicLevel::class, 'academic_level_id');
    }
}
