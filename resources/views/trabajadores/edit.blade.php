@extends('layouts.app')

@section('content')

@section('title', 'Empresas')


<div>
    @livewire('trabajadores.edit-component', ['identificador'=>$id])
</div>

@endsection

