<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    protected $fillable = [
        'created_at',
        'updated_at',
        'deleted_at',
        'status',
        'gender',
        'birthday',
        'curp',
        'person_id'
    ];

    use HasFactory, SoftDeletes;

    public function scopeSearch($query, $searchQuery)
    {
        $formattedQuery = implode(' & ', array_filter(array_map(
            fn($w) => strtolower(trim($w)),
            explode(' ', $searchQuery)
        )));

        return $query->whereRaw("search_text @@ to_tsquery('simple', ?)", [$formattedQuery]);
    }
}
