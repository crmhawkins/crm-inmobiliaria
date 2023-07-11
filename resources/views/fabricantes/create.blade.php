@extends('layouts.app')

@section('content')

@section('title', 'Añadir fabricante')

@section('head')
    @vite(['resources/sass/app.scss'])
@endsection

@section('encabezado', 'Fabricante')
@section('subtitulo', 'Añadir fabricante')
<div>
    @livewire('fabricantes.create-component')
</div>

@endsection

