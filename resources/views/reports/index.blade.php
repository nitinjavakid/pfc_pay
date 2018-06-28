@extends('layouts.master')

@section('title', 'Reports')

@section('content')
<table class="table table-striped">
<tr>
<td>Expense</td>
<td>{{ $expense }}</td>
</tr>
<tr>
<td>Payments received</td>
<td>{{ $payments_received }}</td>
</tr>
<tr>
<td>Payments pending</td>
<td>{{ $payments_pending }}</td>
</tr>
<tr>
<td>Net Amount</td>
<td>{{ $payments_received + $payments_pending - $expense }}</td>
</tr>
</table>

<table class="table table-striped">
    <thead>
        <tr>
           <th scope="col">Date</th>
           <th scope="col">Title</th>
           <th scope="col">Amount</th>
        </tr>
    </thead>
    <tbody>
       @foreach ($pendingea as $ea)
       <tr>
           <td>
               {{ $ea->event->local_time }}
           </td>
           <td>
               <a href="{{ route('events.show', ["id" => $ea->event_id]) }}">
               {{ $ea->event->name }}
               </a>
           </td>
           <td>
               <a href="{{ route('attendees.show', ["id" => $ea->attendee_id]) }}">
               {{ $ea->attendee->name }}
               </a>
           </td>
           <td>
               {{ $ea->event->cost }}
           </td>
       </tr>
       @endforeach
    </tbody>
</table>
@stop