<?php

namespace App\Http\Controllers;

use App\Models\Neumatico;
use App\Http\Requests\StoreNeumaticoRequest;
use App\Http\Requests\UpdateNeumaticoRequest;

class NeumaticoController extends Controller
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
     * @param  \App\Http\Requests\StoreNeumaticoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNeumaticoRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Neumatico  $neumatico
     * @return \Illuminate\Http\Response
     */
    public function show(Neumatico $neumatico)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Neumatico  $neumatico
     * @return \Illuminate\Http\Response
     */
    public function edit(Neumatico $neumatico)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateNeumaticoRequest  $request
     * @param  \App\Models\Neumatico  $neumatico
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNeumaticoRequest $request, Neumatico $neumatico)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Neumatico  $neumatico
     * @return \Illuminate\Http\Response
     */
    public function destroy(Neumatico $neumatico)
    {
        //
    }
}
