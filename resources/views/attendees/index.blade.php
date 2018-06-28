@extends('layouts.master')

@section('title', 'Attendees')

@section('content')
<table class="table table-striped">
    <thead>
        <tr>
           <th scope="col">Name</th>
        </tr>
    </thead>
    <tbody>
       @foreach ($attendees as $attendee)
       <tr>
           <td>
               <a href="{{ route('attendees.show', ["id" => $attendee->id]) }}">
               {{ $attendee['name'] }}
               </a>
           </td>
       </tr>
       @endforeach
    </tbody>
</table>
<center>{{ $attendees->links() }}</center>
@stop