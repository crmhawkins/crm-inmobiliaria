@extends('layouts.app')

@section('content')

@section('title', 'Añadir trabajador')

@section('head')
    @vite(['resources/sass/app.scss'])
@endsection

@section('encabezado', 'Trabajador')
@section('subtitulo', 'Añadir trabajador')
<div>
    @livewire('trabajadores.create-component')
</div>

@endsection

