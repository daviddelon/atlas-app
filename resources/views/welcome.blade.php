<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $locationSlug = $location ?? config('app.default_commune_location');
    @endphp
    <title>Atlas floristique de {{ $communeName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fafaf0; }
        .hero { padding: 3rem 0; text-align: center; }
        .hero h1 { font-size: 2rem; color: #2d5a27; }
        .card-hover { transition: transform 0.3s; }
        .card-hover:hover { transform: scale(1.05); }
    </style>
</head>
<body>
    <div class="hero">
        <div class="container" style="max-width: 1200px;">
            <h1>Atlas floristique de {{ $communeName }}</h1>
        </div>
    </div>

    <section class="pb-5">
        <div class="container" style="max-width: 1200px;">
            <div class="row g-4">
                <div class="col-md-6"><a href="/{{ $locationSlug }}/plantes/angiospermes" class="text-decoration-none"><div class="card card-hover"><img src="/storage/202282.jpg" class="card-img-top" style="height: 200px; object-fit: cover;" alt=""><div class="card-img-overlay d-flex align-items-center justify-content-center"><h5 class="card-title text-white bg-dark bg-opacity-50 p-2 rounded">Plantes à fleurs</h5></div></div></a></div>
                <div class="col-md-6"><a href="/{{ $locationSlug }}/plantes/gymnospermes" class="text-decoration-none"><div class="card card-hover"><img src="/storage/82722.jpg" class="card-img-top" style="height: 200px; object-fit: cover;" alt=""><div class="card-img-overlay d-flex align-items-center justify-content-center"><h5 class="card-title text-white bg-dark bg-opacity-50 p-2 rounded">Conifères</h5></div></div></a></div>
                <div class="col-md-6"><a href="/{{ $locationSlug }}/plantes/fougeres" class="text-decoration-none"><div class="card card-hover"><img src="/storage/346413.jpg" class="card-img-top" style="height: 200px; object-fit: cover;" alt=""><div class="card-img-overlay d-flex align-items-center justify-content-center"><h5 class="card-title text-white bg-dark bg-opacity-50 p-2 rounded">Fougères</h5></div></div></a></div>
                <div class="col-md-6"><a href="/{{ $locationSlug }}/plantes/mousses" class="text-decoration-none"><div class="card card-hover"><img src="/storage/1180933.jpg" class="card-img-top" style="height: 200px; object-fit: cover;" alt=""><div class="card-img-overlay d-flex align-items-center justify-content-center"><h5 class="card-title text-white bg-dark bg-opacity-50 p-2 rounded">Mousses et hépatiques</h5></div></div></a></div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>