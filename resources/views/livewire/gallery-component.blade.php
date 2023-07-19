<div x-data="" x-init="document.addEventListener('DOMContentLoaded', function() {
    $('.owl-carousel').owlCarousel({
        items: 1,
        loop: true,
        margin: 10,
        autoplay: true,
        autoplayTimeout: 3000, //3 Second
    });
});">
    <div class="owl-carousel">
        @foreach ($galeria as $imagen)
            <div> <img src="{{ $imagen }}" alt="Imagen 1"> </div>
        @endforeach
    </div>
</div>
