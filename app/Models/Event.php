<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $connection = 'mysql_event';

    public function date_time(){
        return $this->hasOne(DateTime::class);
    }

    public function health_guideline(){
        return $this->hasOne(HealthGuideline::class);
    }

    public function location(){
        return $this->belongsTo(Location::class);
    }

    public function organizer(){
        return $this->belongsTo(Organizer::class);
    }

    public function event_attendees(){
        return $this->hasMany(EventAttendee::class);
    }
}
