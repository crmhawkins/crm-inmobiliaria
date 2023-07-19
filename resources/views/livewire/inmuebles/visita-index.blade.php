<div style="background-color: #fff">
    @php
        use App\Models\Clientes;
    @endphp
    <br>
    <div class="row justify-content-center">
        <div class="col">
    <h6>Hojas de visita del inmueble</h6>
    <hr/>
    <ul>
        @foreach($hojas_visita as $hoja)
        <li> {{Clientes::where('id', $hoja->cliente_id)->first()->nombre_completo}} - {{substr($hoja->fecha, 0, 10)}}: <a href="{{('../' . $hoja->ruta)}}" class="btn btn-primary"> Ver documento </a></li>
        @endforeach
    </ul>
</div>
</div>

</div>
