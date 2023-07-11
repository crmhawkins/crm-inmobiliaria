@extends('layouts.app')

@section('content')

@section('title', 'Órden de trabajo')


<div>
    @livewire('presupuestos.edit-component', ['identificador'=>$id])
</div>

@endsection

