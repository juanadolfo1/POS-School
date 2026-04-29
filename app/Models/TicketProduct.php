<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketProduct extends Model
{
    protected $fillable = [
        'price',
        'discount',
        'total',
        'ticket_id',
        'pay_concept_id',
        'quantity'
    ];

    use HasFactory, SoftDeletes;
}
