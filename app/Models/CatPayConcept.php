<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatPayConcept extends Model
{
    protected $fillable = [
        'status',
        'label',
        'amount',
        'discount_amount',
        'last_day_with_discount',
        'pay_concept_type',
        'scholar_year_id',
        'id_cat_academic_level',
    ];

    use HasFactory, SoftDeletes;
}
