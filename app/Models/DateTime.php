<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateTime extends Model
{
    use HasFactory;
    protected $connection = 'mysql_event';

    protected $guarded = ['id', 'event_id'];
    protected $casts = [
        'hide_end' => 'boolean',
        'month_long' => 'boolean',
        'year_long' => 'boolean',
        'progress_bar' => 'boolean',
        'all_day' => 'boolean',
    ];


    public function event(){
        return $this->belongsTo(Event::class);
    }
}
