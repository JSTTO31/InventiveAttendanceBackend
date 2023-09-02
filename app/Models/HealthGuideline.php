<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthGuideline extends Model
{
    use HasFactory;
    protected $connection = 'mysql_event';

    protected $guarded = ['id', 'event_id'];
    protected $casts = [
        'enable' => 'boolean',
        'face_mask' => 'boolean',
        'temperature' => 'boolean',
        'physical_distance' => 'boolean',
        'sanitize_before_event' => 'boolean',
        'held_outside' => 'boolean',
        'vaccination' => 'boolean',
    ];
}
