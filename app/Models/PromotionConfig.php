<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromotionConfig extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'default_day',
        'academic_level_id',
        'scholar_year_id',
        'status',
    ];

    public function overrides()
    {
        return $this->hasMany(PromotionConfigOverride::class);
    }

    public function academicLevel()
    {
        return $this->belongsTo(CatAcademicLevel::class, 'academic_level_id');
    }

    public function scholarYear()
    {
        return $this->belongsTo(ScholarYear::class, 'scholar_year_id');
    }
}
