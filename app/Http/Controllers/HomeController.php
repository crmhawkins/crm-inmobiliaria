<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        return view('seleccion');
    }

    public function home(Request $request)
    {
        $boton = $request->query('boton');

        if ($boton == 'sayco') {
            $request->session()->put('inmobiliaria', 'sayco');
        } else if ($boton == 'sancer') {
            $request->session()->put('inmobiliaria', 'sancer');
        }

        $user = $request->user();
        return view('agenda.index', compact('user'));

    }

    public function cambio(Request $request)
    {
        $boton = $request->query('boton');

        if ($boton == 'sayco') {
            $request->session()->put('inmobiliaria', 'sayco');
        } else if ($boton == 'sancer') {
            $request->session()->put('inmobiliaria', 'sancer');
        }
            return redirect()->back();
    }
}
