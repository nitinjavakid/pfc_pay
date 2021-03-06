@extends('layouts.master')

@section('title', 'Events')

@section('content')
<table class="table table-striped">
    <thead>
        <tr>
           <th scope="col">Date</th>
           <th scope="col">Title</th>
           <th scope="col">Status</th>
           <th scope="col">Total</th>
        </tr>
    </thead>
    <tbody>
       @foreach ($events as $event)
       <tr>
           <td>
               {{ $event->local_time }}
           </td>
           <td>
               <a href="{{ route('events.show', ["id" => $event->id]) }}">
               {{ $event['name'] }}
               </a>
           </td>
           <td>
               {{ $event['status'] }}
           </td>
           <td>
           {{ $event->payments->where('status', '=', 'paid')
                    ->sum("net_amount") - $event['water'] - $event['ground'] }}
           </td>
       </tr>
       @endforeach
    </tbody>
</table>
<center>{{ $events->links() }}</center>
@stop