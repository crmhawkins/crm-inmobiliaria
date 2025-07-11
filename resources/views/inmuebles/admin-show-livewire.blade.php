@extends('layouts.app')

@section('encabezado', 'Detalle del inmueble - Admin')
@section('subtitulo', 'Vista de administración')

@section('content')
    @livewire('inmuebles.admin-show', ['identificador' => $id], key('admin-show-' . $id))
@endsection
