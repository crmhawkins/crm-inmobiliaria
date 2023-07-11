<div class="container-fluid" style="background-color: #f9f9f9; margin-top:30px; min-height:100%;">
    <div style="border-bottom: 1px solid black; margin-bottom:10px;">
    @mobile
        <div class="row">
            <h1 style="border-bottom:0px !important">@yield('encabezado')</h1>
            <h2 style="text-align:left;">@yield('subtitulo')</h2>
        </div>
    @elsemobile
        <div class="row">
            <h1 class="col-8" style="border-bottom:0px !important">@yield('encabezado')</h1>
            <h2 class="col-4 align-self-end" style="text-align:right;">@yield('subtitulo')</h2>
        </div>
    @endmobile
    </div>
    <div class="container-fluid shadow-lg" style="background-color: #fcfcfc; min-height:100%">
        <div>
            @yield('content')
        </div>
    </div>
</div>
