@extends('layouts.app')

@section('content')

@livewire('proveedores.edit-component', ['identificador' => $id])

@endsection