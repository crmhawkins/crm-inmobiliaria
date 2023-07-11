@extends('layouts.app')

@section('content')
@section('title', 'Productos - Categorías')
@section('encabezado', 'Categorías')
@section('subtitulo', 'Consulta')
<div>
    @livewire('productoscategories.tabs-component')
</div>

 @endsection
