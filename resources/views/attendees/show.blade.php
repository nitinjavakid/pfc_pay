@extends('layouts.master')

@section('title', 'Attendees')

@section('content')
<h4>Name: {{ $attendee->name }}</h4>
<table class="table table-striped">
    <thead>
        <tr>
           <th scope="col">Date</th>
           <th scope="col">Title</th>
           <th scope="col">Amount</th>
        </tr>
    </thead>
    <tbody>
       @foreach ($pending as $event)
       <tr>
           <td>
               {{ $event->event->local_time }}
           </td>
           <td>
               <a href="{{ route('events.show', ["id" => $event->event->id]) }}">
               {{ $event->event->name }}
               </a>
           </td>
           <td>
               {{ $event->event->cost }}
           </td>
       </tr>
       @endforeach
    </tbody>
</table>
@stop
