@extends('layouts.app')


@section('head')
    @vite(['resources/sass/app.scss'])
@endsection

@section('content')
@section('encabezado', 'Clientes')
@section('subtitulo', 'Editando ' . $id)
@livewire('clients.edit-component', ['identificador' => $id])

@endsection
