@section('head')
    @vite(['resources/sass/settings.scss'])
@endsection
<div class="container">
    <div class="d-flex justify-content-evenly align-items-center">
        <h1 class="titulo">Datos de la Empresa</h1>
        @if (isset($empresa))
        <a href="{{route('settings.edit')}}" class="btn btn-info text-white rounded-circle"><i class="fa-solid fa-pen-to-square"></i></a>
        @else
        <a href="{{route('settings.create')}}" class="btn btn-info text-white rounded-circle"><i class="fa-solid fa-plus"></i></a>
        @endif
    </div>
    <hr class="pb-2">

    @if (isset($empresa))
    {{-- <img src='{{$empresa->photo->temporaryUrl()}}' alt="" title=""></a> --}}
    <img src="/assets/{{$empresa->photo}}" alt="" title="" class="img-fluid" style="max-width: 200px"></a>
    {{$file}}

    <div class="mb-3 row d-flex align-items-center">
        <label for="name" class="col-md-2 col-form-label"><strong>Nombre de la Compañia</strong></label>
        <div class="col-md-10">
            <p class="datos-empresa">{{$empresa->name}}</p>
        </div>
    </div>
    <div class="mb-3 row d-flex align-items-center">
        <label for="name" class="col-md-2 col-form-label"><strong>CIF</strong></label>
        <div class="col-md-10">
            <p class="datos-empresa">{{$empresa->taxNumber}}</p>
        </div>
    </div>
    <div class="mb-3 row d-flex align-items-center">
        <label for="name" class="col-md-2 col-form-label"><strong>Direccion de la Compañia</strong></label>
        <div class="col-md-10">
            <p class="datos-empresa">{{$empresa->adress}}</p>
        </div>
    </div>
    <div class="mb-3 row d-flex align-items-center">
        <label for="name" class="col-md-2 col-form-label"><strong>Ciudad</strong></label>
        <div class="col-md-10">
            <p class="datos-empresa">{{$empresa->ciudad}}</p>
        </div>
    </div>
    <div class="mb-3 row d-flex align-items-center">
        <label for="name" class="col-md-2 col-form-label"><strong>Provincia</strong></label>
        <div class="col-md-10">
            <p class="datos-empresa">{{$empresa->province}}</p>
        </div>
    </div>
    <div class="mb-3 row d-flex align-items-center">
        <label for="name" class="col-md-2 col-form-label"><strong>Codigo Postal</strong></label>
        <div class="col-md-10">
            <p class="datos-empresa">{{$empresa->postCode}}</p>
        </div>
    </div>
    @else
    <h3>Todavia no se a generado ninguna empresa</h3>
    @endif
        
        
</div>
