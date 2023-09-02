<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Student;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request){
        $eventPaginated = collect(Event::with(['health_guideline', 'location', 'organizer', 'date_time'])->paginate(4));
        $events = $eventPaginated['data'];
        unset($eventPaginated['data']);

        return [
            'events' => collect($events)->map(function($event){
                return [
                    ...$event,
                    'attendees' => Student::whereHas('attendances', fn($query) => $query->where('event_id', $event['id']))->get()
                ];
            }),
            'options' => $eventPaginated,
        ];
    }

    public function show(Request $request, Event $event){
        $event->load(['health_guideline', 'location', 'organizer', 'date_time']);
        $event = collect($event);
        $event['attendees'] = Student::whereHas('attendances', fn($query) => $query->where('event_id', $event['id']))->get();

        return $event;
    }
}
