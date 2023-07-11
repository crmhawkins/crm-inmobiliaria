@extends('layouts.app')


@section('title', 'Alumnos')

@section('head')
    @vite(['resources/sass/app.scss'])
@endsection

@section('encabezado', 'Ecotasa')
@section('subtitulo', 'Editar ecotasa')

@section('content')
<div>

@livewire('ecotasa.edit', ['identificador' => $id])
</div>

@endsection
