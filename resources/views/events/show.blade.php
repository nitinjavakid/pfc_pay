@extends('layouts.master')

@section('content')
<div class="panel-group">
<div class="panel panel-default">
    <div class="panel-heading">
    <h4 class="panel-title">
        <a data-toggle="collapse" href="#collapse1">Event details</a>
        <span style="float: right">
        Cash - {{ $event->payments->where('type', '=', 'cash')->where('status', 'paid')->sum("net_amount") }} |
        PayTM - {{ $event->payments->where('type', '=', 'paytm')->where('status', 'paid')->sum("net_amount") }} |
        Instamojo - {{ $event->payments->where('type', '=', 'instamojo')->where('status', 'paid')->sum("net_amount") }}
        </span>
    </h4>
    </div>
    <div id="collapse1" class="panel-collapse collapse">
    <div class="panel-body">
{{ Form::open(['route' => ["events.update", $event->id], "method" => "put" ]) }}
<div class="form-group row">
    <label for="name" class="col-sm-2 col-form-label">Event name</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="name" value="{{ $event->name }}" name="name" disabled="disabled" />
    </div>
</div>
<div class="form-group row">
    <label for="date" class="col-sm-2 col-form-label">Event time</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="date" value="{{ $event->local_time }}"  disabled="disabled" name="date" />
    </div>
</div>
<div class="form-group row">
    <label for="cost" class="col-sm-2 col-form-label">Event cost</label>
    <div class="col-sm-10">
    <input type="text" class="form-control" id="cost" value="{{ $event->cost }}" {{ Gate::allows('update', $event) ? "" : 'disabled="disabled"' }} name="cost" />
    </div>
</div>
<div class="form-group row">
    <label for="water" class="col-sm-2 col-form-label">Water cost</label>
    <div class="col-sm-10">
    <input type="text" class="form-control" id="cost" value="{{ $event->water }}" {{ Gate::allows('update', $event) ? "" : 'disabled="disabled"' }} name="water" />
    </div>
</div>
<div class="form-group row">
    <label for="ground" class="col-sm-2 col-form-label">Ground cost</label>
    <div class="col-sm-10">
    <input type="text" class="form-control" id="ground" value="{{ $event->ground }}" {{ Gate::allows('update', $event) ? "" : 'disabled="disabled"' }} name="ground" />
    </div>
</div>
<div class="form-group row">
    <label for="comment" class="col-sm-2 col-form-label">Comment</label>
    <div class="col-sm-10">
    <input type="text" class="form-control" id="comment" value="{{ $event->comment }}" {{ Gate::allows('update', $event) ? "" : 'disabled="disabled"' }} name="comment" />
    </div>
</div>

    @if(Gate::allows('update', $event))
    <center>
        @if(Gate::allows('cash', $event))
        <input name="settled" type='checkbox' value='settled' /> Settled
        @endif
        <input type="submit" value="Update" class="btn btn-primary" />
    </center>
    @endif
{{ Form::close() }}
    </div>
    </div>
  </div>
</div>


{{Form::open(['route' => ["events.pay", $event->id]]) }}
<table class="table table-striped">
    <thead>
        <tr>
           <th scope="col">Attendee</th>
           <th scope="col">Status</th>
        </tr>
    </thead>
    <tbody>
       @foreach ($event->sortedAttendees() as $attendee)
       <tr>
           <td>
               @if($attendee->payment == null)
               <input type="checkbox" name="attendee[]" value="{{ $attendee->id }}" />
               @endif
               {{ $attendee->attendee->name }}
               @if($attendee->guest)
                 's guest
               @endif
           </td>
           <td>
           {{ $attendee->payment == null ? "Pending" : $attendee->payment->type . " - " . $attendee->payment->net_amount/ $attendee->payment->paid_for->count() }}
           </td>
       </tr>
       @endforeach
    </tbody>
</table>
<center>
{!! Captcha::display() !!}
</center>
<input type="hidden" name="type" id="type"/>
<center>
    <input type="submit" class="btn btn-primary" value="Pay using Instamojo" onclick='$("#type").val("instamojo"); return true;'>
    @if(Gate::allows('cash', $event))
    <div class="form-inline">
    New Cost:
    <input type="text" name="newcost" class="form-control" value="{{$event->cost}}">
    <input type="submit" class="btn btn-primary" value="Pay using Cash" onclick='$("#type").val("cash"); return true;'>
    <input type="submit" class="btn btn-primary" value="Pay using Paytm" onclick='$("#type").val("paytm"); return true;'>
    </div>
    @endif
</center>
</form>
{{Form::close() }}

<table class="table table-striped">
    <thead>
        <tr>
           <th scope="col">Time</th>
           <th scope="col">User</th>
           <th scope="col">Description</th>
        </tr>
    </thead>
    <tbody>
@foreach ($event->history as $history)
    <tr>
       <td>{{ $history->local_time }}</th>
       <td>{{ $history->user->name }}</td>
       <td><pre>{{ $history->description }}</pre></td>
    </tr>
@endforeach
    </tbody>
</table>
@stop
