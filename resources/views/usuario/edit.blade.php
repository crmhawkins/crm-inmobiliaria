@extends('layouts.app')

@section('content')

@section('title', 'Empresas')


<div>
    @livewire('usuarios.edit-component', ['identificador'=>$id])
</div>

@endsection

