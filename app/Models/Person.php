<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = [
        'created_at',
        'updated_at',
        'deleted_at',
        'email',
        'name',
        'first_lastname',
        'second_lastname'
    ];
}
