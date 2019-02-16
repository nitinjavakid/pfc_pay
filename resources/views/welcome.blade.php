@extends('layouts.master')

@section('title', 'Home')

@section('content')
<center>
  <div class="btn-group-vertical">
  @auth
  <a class="btn btn-default btn-lg" href="{{ route('me') }}">My actions</a>
  <a class="btn btn-default btn-lg" href="{{ route('passbook') }}">Passbook</a>
  @endauth
  <a class="btn btn-default btn-lg" href="{{ route('events.index') }}">Events</a>
  <a class="btn btn-default btn-lg" href="{{ route('attendees.index') }}">Attendees</a>
  <a class="btn btn-default btn-lg" href="{{ route('reports.index') }}">Reports</a>
  </div>
</center>
@stop
