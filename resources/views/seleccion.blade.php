<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css"
        integrity="sha512-t4GWSVZO1eC8BM339Xd7Uphw5s17a86tIZIj8qRxhnKub6WoyhnrxeCIMeAqBPgdZGlCcG2PrZjMc+Wr78+5Xg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"
        integrity="sha512-3dZ9wIrMMij8rOH7X3kLfXAzwtcHpuYpEgQg1OA4QAob1e81H8ntUQmQm3pBudqIoySO5j0tHN4ENzA6+n2r4w=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
        integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</head>

<body>
    <div class="container-fluid h-100" style="background-color:#121828;">
        <div class="row justify-content-center mb-5">
            <div class="col text-center">
                <br>
                <h1 class="text-white">¿Dónde quieres acceder?</h1>
            </div>
        </div>
        <div class="row justify-content-center mb-5">
            <div class="col-9">
                <a href="{{route('home', ['boton' => 'sayco'])}}" class="btn btn-primary"
                    style="width:100% !important; height:150% !important; margin-bottom: -40px !important;">
                    <h1><img src="{{ asset('images/logosayco.png') }}"></h1>
            </a>
            </div>
        </div>
        <div class="row justify-content-center mt-5">
            <div class="col-9">
                <a href="{{route('home', ['boton' => 'sancer'])}}"  class="btn btn-primary" style="width:100% !important; height:150% !important; margin-bottom: -40px !important;">
                    <h1><img src="{{ asset('images/logosancer.png') }}"></h1>
                </a>
            </div>
        </div>
    </div>
</body>
