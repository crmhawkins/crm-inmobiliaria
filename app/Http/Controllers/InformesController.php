<?php

namespace App\Http\Controllers;

use App\Models\CobroCaja;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InformesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('informes.index');
    }
}
