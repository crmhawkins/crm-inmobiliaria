@extends('layouts.app')

@section('content')

@section('title', 'Empresas')


<div>
    @livewire('fabricantes.edit-component', ['identificador'=>$id])
</div>

@endsection

