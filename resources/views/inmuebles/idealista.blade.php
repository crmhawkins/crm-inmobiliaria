@extends('layouts.app')

@section('encabezado', 'Inmuebles de Idealista')
@section('subtitulo', 'Consulta de inmuebles importados desde Idealista')

@section('content')
    @livewire('inmuebles.idealista-index')
@endsection

