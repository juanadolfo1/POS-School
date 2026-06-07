<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolConfig extends Model
{
    protected $table = 'school_config';

    protected $fillable = [
        'school_name',
        'favicon_path',
        'logo_path',
    ];
}
