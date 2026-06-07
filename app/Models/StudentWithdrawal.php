<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentWithdrawal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'scholar_year_id',
        'type',
        'reason',
        'effective_date',
        'reactivated_at',
        'status',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'reactivated_at' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scholarYear()
    {
        return $this->belongsTo(ScholarYear::class);
    }

    public function isActive(): bool
    {
        return $this->status === 1;
    }
}
