@extends('layouts.app')


@section('title', 'Alumnos')

@section('head')
    @vite(['resources/sass/app.scss'])
@endsection

@section('encabezado', 'Proveedor')
@section('subtitulo', 'Crear proveedor')

@section('content')
<div>
    @livewire('proveedores.create-component')
</div>

@endsection

