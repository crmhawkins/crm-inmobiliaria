@extends('layouts.app')


@section('title', 'Alumnos')

@section('head')
    @vite(['resources/sass/app.scss'])
@endsection

@section('encabezado', 'Cliente')
@section('subtitulo', 'Crear cliente')

@section('content')
<div>
    @livewire('clients.create-component')
</div>

@endsection

