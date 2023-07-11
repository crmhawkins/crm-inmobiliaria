<?php

namespace App\Http\Controllers;

use App\Models\Ecotasa;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEcotasaRequest;
use App\Http\Requests\UpdateEcotasaRequest;

class EcotasaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('ecotasa.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('ecotasa.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEcotasaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEcotasaRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ecotasa  $ecotasa
     * @return \Illuminate\Http\Response
     */
    public function show(Ecotasa $ecotasa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ecotasa  $ecotasa
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('ecotasa.edit', compact('id'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEcotasaRequest  $request
     * @param  \App\Models\Ecotasa  $ecotasa
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEcotasaRequest $request, Ecotasa $ecotasa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ecotasa  $ecotasa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ecotasa $ecotasa)
    {
        //
    }
}
