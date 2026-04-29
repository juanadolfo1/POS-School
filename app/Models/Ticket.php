<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    protected $fillable = [
        'is_full_payed',
        'amount',
        'has_discount',
        'discount_type',
        'discount_amount',
        'folio_ticket',
        'payment_method_id',
        'student_group_id'
    ];

    use HasFactory, SoftDeletes;
}
