@extends('layouts.app')

@section('content')

@section('title', 'Presupuestos')


<div>
    @livewire('presupuestos.edit-component', ['identificador'=>$id])
</div>

@endsection

