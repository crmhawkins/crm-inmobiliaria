<?php

namespace App\Http\Controllers;

use App\Models\Proveedores;
use App\Http\Requests\StoreProveedoresRequest;
use App\Http\Requests\UpdateProveedoresRequest;

class ProveedoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $response = 'Hola Proveedoreses Nacho!!!';
        // $user = Auth::user();

        return view('proveedores.index', compact('response'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('proveedores.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProveedoresRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProveedoresRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Proveedores  $Proveedores
     * @return \Illuminate\Http\Response
     */
    public function show(Proveedores $Proveedores)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Proveedores  $Proveedores
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('proveedores.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProveedoresRequest  $request
     * @param  \App\Models\Proveedores  $Proveedores
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProveedoresRequest $request, Proveedores $Proveedores)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Proveedores  $Proveedores
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proveedores $Proveedores)
    {
        //
    }
}