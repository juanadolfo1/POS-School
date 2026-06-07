<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionConfigOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_config_id',
        'month',
        'deadline_date',
    ];

    protected $casts = [
        'deadline_date' => 'date',
    ];

    public function config()
    {
        return $this->belongsTo(PromotionConfig::class, 'promotion_config_id');
    }
}
