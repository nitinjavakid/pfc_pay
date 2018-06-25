@extends('layouts.master')

@section('title', 'Events')

@section('content')
<table class="table table-striped">
    <thead>
        <tr>
           <th scope="col">Date</th>
           <th scope="col">Title</th>
           <th scope="col">Status</th>
           <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
       @foreach ($events as $event)
       <tr>
           <td>
               {{ $event->local_time }}
           </td>
           <td>
               {{ $event['name'] }}
           </td>
           <td>
               {{ $event['status'] }}
           </td>
           <td>
               <a href="{{ route('events.show', ["id" => $event->id]) }}">
                   Manage
               </a>
           </td>
       </tr>
       @endforeach
    </tbody>
</table>
<center>{{ $events->links() }}</center>
@stop