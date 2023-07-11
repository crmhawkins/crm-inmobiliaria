<!DOCTYPE html>
<html lang="en">

<style>
    html {
        margin: 0px;
    }

    .imagen-fondo {
        display: block;
        width: 100%;
    }

    .sidebar {
        width: 20px;
        height: 100%;
        background-color: #FA3804;
        position: fixed;
        /* posición fija para que la barra lateral se mantenga visible mientras se desplaza la página */
        float: right;
    }

    .textoAcreditar{
        text-align: center;
        font-size: 180%;
        color: grey;
    }

    .textoNombre{
        text-align: center;
    }

    .textoDNI{
        text-align: center;
        font-size: 150%;

    }

    .textoCurso{
        text-align: center;
        color: #FA3804;
        font-size: 300%;

    }

    .textoDescripcion{
        text-align: center;
        font-size: 130%;
        margin-left: 5%;
        margin-right: 5%;

    }

    .imagen-firma {
        display: block;
        width: 90%;
    }

    .textoFooter {
        text-align: center;
        font-size: 80%;
    }
    body {
            font-family: "NeoTechStd", sans-serif;
        }

    /* Página 2 */

    .pagina2{
        margin-left: 5%;
        margin-right: 20px;
    }
    .pagina2 h2{
    color: black !important;
}
    .pagina2 h3{
        color: #FA3804 !important;
    }
    .pagina2 h4{
        color: #FA3804 !important;
        margin-bottom: -3px

    }
    .pagina2 p{
        margin-bottom: -6px
    }

    .sideimage {
        float: right;
        height: 100%;
    }
      .sideimage img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }

</style>




<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Certificado</title>
</head>

<body>
    <div class="sidebar">
        <!-- Contenido de la barra lateral -->
    </div>
    <div class="contenedor-imagen">
        <img src="{{ public_path('/assets/backgroundTotal.PNG') }}" alt="Imagen de fondo" class="imagen-fondo">

        <p class="textoAcreditar">Acreditan en <strong> {{$cursoCelebracion->nombre}} a {{ $cursoFechaCelebracion }} </strong> que</p>

        <h1 class="textoNombre">D. {{ $alumno->nombre }} {{ $alumno->apellidos }}</h1>

        <p class="textoDNI">Con DNI {{$alumno->dni}} ha superado con éxito el curso, </p>

        <h1 class="textoCurso"> {{ $curso->nombre }}</h1>

        <p class="textoDescripcion">impartido por el Centro de Formación e Investigación de Riesgos de Trabajos en Altura,
             con una carga lectiva total de <strong> {{ $curso->duracion }} horas </strong> celebrado el día {{$cursoFechaCelebracionConBarras}}.
             El temario corresponde a las materias Prácticas y Teóricas descritas en el reverso. </p>

        <img src="{{ public_path('/assets/firmas.PNG') }}" alt="Imagen de fondo" class="imagen-firma">

        <p class="textoFooter">Formal S.L. www.formal.es | Camino de la Ermita, 10. Polígono Industrial Gibraltar 11300 La Línea de la Concepción, Cádiz, España T. +34 956 763 055 | F. +34 956 690 254      </p>

    </div>

    <div class="pagina2">

        <div class="sideimage">
            <img src="{{ public_path('/assets/backgroundLateral.PNG') }}" alt="Imagen de fondo" class="imagen-fondo">
        </div>

        <div>{!! html_entity_decode($curso->descripcion) !!}</div>

    </div>




</body>

</html>
