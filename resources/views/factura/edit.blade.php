@extends('layouts.app')

@section('content')

@section('title', 'Facturas')


<div>
    @livewire('facturas.edit-component', ['identificador'=>$id])
</div>

@endsection

