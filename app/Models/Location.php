<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $connection = 'mysql_event';

    protected $guarded = ['id'];
    protected $casts = [
        'open_new_tab' => 'boolean'
    ];
}
