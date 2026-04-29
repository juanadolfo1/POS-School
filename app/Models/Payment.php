<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    protected $fillable = [
        'is_full_payment',
        'paid_amount',
        'paid_at',
        'ticket_product_id',
        'applied_discount',
    ];

    use HasFactory, SoftDeletes;
}
