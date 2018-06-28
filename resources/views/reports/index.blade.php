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
@stop
