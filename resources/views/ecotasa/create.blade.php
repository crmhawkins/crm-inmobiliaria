@extends('layouts.app')


@section('title', 'Alumnos')

@section('head')
    @vite(['resources/sass/app.scss'])
@endsection

@section('encabezado', 'Ecotasa')
@section('subtitulo', 'Añadir ecotasa')

@section('content')
<div>
    @livewire('ecotasa.create')
</div>

@endsection

