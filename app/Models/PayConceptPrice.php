<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**Deprecated */
class PayConceptPrice extends Model
{
    protected $fillable = [
        'status',
        'price',
        'scholar_year_id',
        'pay_concept_id'
    ];

    use HasFactory, SoftDeletes;
}
