<?php

namespace App\Http\Controllers;

use App\Models\ListaAlmacen;
use App\Http\Requests\StoreListaAlmacenRequest;
use App\Http\Requests\UpdateListaAlmacenRequest;

class ListaAlmacenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreListaAlmacenRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreListaAlmacenRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ListaAlmacen  $listaAlmacen
     * @return \Illuminate\Http\Response
     */
    public function show(ListaAlmacen $listaAlmacen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ListaAlmacen  $listaAlmacen
     * @return \Illuminate\Http\Response
     */
    public function edit(ListaAlmacen $listaAlmacen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateListaAlmacenRequest  $request
     * @param  \App\Models\ListaAlmacen  $listaAlmacen
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateListaAlmacenRequest $request, ListaAlmacen $listaAlmacen)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ListaAlmacen  $listaAlmacen
     * @return \Illuminate\Http\Response
     */
    public function destroy(ListaAlmacen $listaAlmacen)
    {
        //
    }
}
