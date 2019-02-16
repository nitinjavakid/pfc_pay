@extends('layouts.master')

@section('title', 'Passbook')

@section('content')
<table class="table table-striped">
    <thead>
        <tr>
           <th scope="col">Date</th>
           <th scope="col">Event</th>
           <th scope="col">Event Date</th>
           <th scope="col">Type</th>
           <th scope="col">Cost</th>
        </tr>
    </thead>
    <tbody>
       @foreach ($payments as $payment)
       <tr>
           <td>
               {{ Util::local_time($payment->updated_at) }}
           </td>
           <td>
               <a href="{{ route('events.show', ["id" => $payment->event->id]) }}">
               {{ $payment->event->name }}
               </a>
           </td>
           <td>
               {{ $payment->event->local_time }}
           </td>
           <td>
               {{ $payment->type }}
           </td>
           <td>
               {{ $payment->net_amount / count($payment->paid_for) }}
           </td>
       </tr>
       @endforeach
    </tbody>
</table>
<center>{{ $payments->links() }}</center>
@stop
