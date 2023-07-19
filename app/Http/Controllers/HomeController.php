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
        $previousUrl = URL::previous();

        $boton = $request->query('boton');

        if ($boton == 'sayco') {
            $request->session()->put('inmobiliaria', 'sayco');
        } else if ($boton == 'sancer') {
            $request->session()->put('inmobiliaria', 'sancer');
        }

        if (str_contains($previousUrl, '/seleccion') || str_contains($previousUrl, '/home') || str_contains($previousUrl, '/home')) {
            $user = $request->user();
            return view('agenda.index', compact('user'));
        } else if (str_contains($previousUrl, '/home') || str_contains($previousUrl, '/home')) {
            $user = $request->user();
            return redirect()->route('seleccion', compact('user'));
        } else {
            return redirect()->back();
        }
    }
}
