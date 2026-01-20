<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atlas floristique de Saint-Martin-de-Londres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #fafaf0; }
        .hero-bg { background-image: url('https://placehold.co/1440x420'); background-size: cover; background-position: center; position: relative; }
        .hero-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.35); }
        .hero-content { position: relative; z-index: 2; }
        .stats-bg { background-color: #e8f5e8; }
        .atlas-section { padding: 4.5rem 0; }
        .saisons-bg { background-color: #fff; }
        .focus-bg { background-color: #f0f8f0; }
        .methodologie-bg { background-color: #fafaf0; }
        .contribution-bg { background-color: #fff; }
        .random-bg { background-image: url('https://placehold.co/1200x200'); background-size: cover; }
        .footer-bg { background-color: #2d5016; color: white; }
        .btn-saison { width: 96px; height: 96px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
        .btn-saison:hover { background-color: #28a745; color: white; }
        .card-hover { transition: transform 0.3s; }
        .card-hover:hover { transform: scale(1.05); }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm" style="height: 72px;">
        <div class="container" style="max-width: 1200px;">
            <a class="navbar-brand" href="#">Atlas floristique de Saint-Martin-de-Londres</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="#">Explorer</a>
                <a class="nav-link" href="#">Carte</a>
                <a class="nav-link" href="#">Méthodologie</a>
                <a class="nav-link" href="#">À propos</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero-bg text-white text-center" style="height: 420px;">
        <div class="hero-overlay"></div>
        <div class="hero-content d-flex align-items-center justify-content-center h-100">
            <div class="container" style="max-width: 1200px;">
                <h1 class="display-4" style="font-size: 44px; line-height: 1.2;">Titre principal</h1>
                <p class="lead" style="font-size: 18px; opacity: 0.9; max-width: 640px; margin: 24px auto 48px;">Texte d'accroche ici.</p>
                <div class="d-flex justify-content-center">
                    <input class="form-control" style="width: 640px; height: 56px; border-radius: 12px;" placeholder="Rechercher...">
                    <button class="btn btn-light ms-2" style="width: 56px; height: 56px;"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Chiffres clés -->
    <section class="stats-bg py-3">
        <div class="container" style="max-width: 1200px;">
            <div class="row text-center">
                <div class="col"><i class="bi bi-tree"></i><div class="h4">1234</div><div class="small">Espèces</div></div>
                <div class="col"><i class="bi bi-geo-alt"></i><div class="h4">56</div><div class="small">Milieux</div></div>
                <div class="col"><i class="bi bi-camera"></i><div class="h4">7890</div><div class="small">Photos</div></div>
                <div class="col"><i class="bi bi-people"></i><div class="h4">123</div><div class="small">Contributeurs</div></div>
                <div class="col"><i class="bi bi-calendar"></i><div class="h4">2023</div><div class="small">Année</div></div>
            </div>
        </div>
    </section>

    <!-- Explorer l'atlas -->
    <section class="atlas-section">
        <div class="container" style="max-width: 1200px;">
            <h2 class="text-center mb-5">Explorer l'atlas</h2>
            <!-- Grands groupes -->
            <div class="row g-4 mb-5">
                <div class="col-md-6"><a href="/plantes/angiospermes" class="text-decoration-none"><div class="card card-hover"><img src="/storage/202282.jpg" class="card-img-top" style="height: 200px; object-fit: cover;" alt=""><div class="card-img-overlay d-flex align-items-center justify-content-center"><h5 class="card-title text-white bg-dark bg-opacity-50 p-2 rounded">Plantes à fleurs</h5></div></div></a></div>
                <div class="col-md-6"><a href="/plantes/gymnospermes" class="text-decoration-none"><div class="card card-hover"><img src="/storage/82722.jpg" class="card-img-top" style="height: 200px; object-fit: cover;" alt=""><div class="card-img-overlay d-flex align-items-center justify-content-center"><h5 class="card-title text-white bg-dark bg-opacity-50 p-2 rounded">Conifères</h5></div></div></a></div>
                <div class="col-md-6"><a href="/plantes/fougeres" class="text-decoration-none"><div class="card card-hover"><img src="/storage/346413.jpg" class="card-img-top" style="height: 200px; object-fit: cover;" alt=""><div class="card-img-overlay d-flex align-items-center justify-content-center"><h5 class="card-title text-white bg-dark bg-opacity-50 p-2 rounded">Fougères</h5></div></div></a></div>
                <div class="col-md-6"><a href="/plantes/mousses" class="text-decoration-none"><div class="card card-hover"><img src="/storage/1180933.jpg" class="card-img-top" style="height: 200px; object-fit: cover;" alt=""><div class="card-img-overlay d-flex align-items-center justify-content-center"><h5 class="card-title text-white bg-dark bg-opacity-50 p-2 rounded">Mousses et hépatiques</h5></div></div></a></div>
            </div>
            <!-- Milieux naturels -->
            <div class="row g-3">
                <div class="col-md-4"><div class="card"><img src="https://placehold.co/300x200" class="card-img-top" alt=""><div class="card-body"><i class="bi bi-tree"></i><p class="card-text">Forêt</p></div></div></div>
                <div class="col-md-4"><div class="card"><img src="https://placehold.co/300x200" class="card-img-top" alt=""><div class="card-body"><i class="bi bi-sun"></i><p class="card-text">Prairie</p></div></div></div>
                <div class="col-md-4"><div class="card"><img src="https://placehold.co/300x200" class="card-img-top" alt=""><div class="card-body"><i class="bi bi-water"></i><p class="card-text">Rivière</p></div></div></div>
                <div class="col-md-4"><div class="card"><img src="https://placehold.co/300x200" class="card-img-top" alt=""><div class="card-body"><i class="bi bi-mountain"></i><p class="card-text">Montagne</p></div></div></div>
                <div class="col-md-4"><div class="card"><img src="https://placehold.co/300x200" class="card-img-top" alt=""><div class="card-body"><i class="bi bi-house"></i><p class="card-text">Urbain</p></div></div></div>
                <div class="col-md-4"><div class="card"><img src="https://placehold.co/300x200" class="card-img-top" alt=""><div class="card-body"><i class="bi bi-flower1"></i><p class="card-text">Jardin</p></div></div></div>
            </div>
        </div>
    </section>

    <!-- Saisons -->
    <section class="saisons-bg py-5">
        <div class="container" style="max-width: 1200px;">
            <h2 class="mb-4">Saisons</h2>
            <div class="d-flex gap-3">
                <button class="btn btn-outline-secondary btn-saison"><i class="bi bi-snow"></i><br>Hiver</button>
                <button class="btn btn-outline-secondary btn-saison"><i class="bi bi-flower1"></i><br>Printemps</button>
                <button class="btn btn-outline-secondary btn-saison"><i class="bi bi-sun"></i><br>Été</button>
                <button class="btn btn-outline-secondary btn-saison"><i class="bi bi-tree"></i><br>Automne</button>
            </div>
        </div>
    </section>

    <!-- Focus local -->
    <section class="focus-bg py-5">
        <div class="container" style="max-width: 1200px;">
            <div class="row">
                <div class="col-md-6"><div class="card"><img src="https://placehold.co/500x300" class="card-img-top" alt=""><div class="card-body"><h5>Espèces remarquables</h5><p>Texte court.</p><button class="btn btn-secondary">Voir</button></div></div></div>
                <div class="col-md-6"><div class="card"><img src="https://placehold.co/500x300" class="card-img-top" alt=""><div class="card-body"><h5>Carte floristique</h5><p>Mini carte.</p><button class="btn btn-primary">Explorer</button></div></div></div>
            </div>
        </div>
    </section>

    <!-- Méthodologie -->
    <section class="methodologie-bg py-5">
        <div class="container text-center" style="max-width: 720px;">
            <div class="d-flex justify-content-center gap-4 mb-3">
                <i class="bi bi-book fs-2"></i>
                <i class="bi bi-dna fs-2"></i>
                <i class="bi bi-camera fs-2"></i>
            </div>
            <p style="font-size: 16px; line-height: 1.6;">Texte méthodologie ici.</p>
        </div>
    </section>

    <!-- Contribution -->
    <section class="contribution-bg py-5">
        <div class="container" style="max-width: 640px;">
            <div class="card border-success">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center gap-3 mb-3">
                        <i class="bi bi-camera fs-3"></i>
                        <i class="bi bi-eye fs-3"></i>
                        <i class="bi bi-pencil fs-3"></i>
                    </div>
                    <button class="btn btn-success btn-lg">Contribuer</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Découverte aléatoire -->
    <section class="random-bg text-white text-center py-5">
        <div class="container">
            <h3>Découverte aléatoire</h3>
            <button class="btn btn-light btn-lg mt-3">Découvrir</button>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-bg py-4">
        <div class="container" style="max-width: 1200px;">
            <div class="row">
                <div class="col-md-4"><h5>À propos</h5><p>Texte.</p></div>
                <div class="col-md-4"><h5>Sources & licences</h5><p>Texte.</p></div>
                <div class="col-md-4"><h5>Contact</h5><p>Texte.</p></div>
            </div>
            <hr class="my-3">
            <div class="text-center small">Mentions légales | Crédits</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>