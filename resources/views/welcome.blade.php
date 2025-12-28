@extends('layouts.app')

{{--
  Ce fichier Blade représente la page d'accueil de l'Atlas de la Biodiversité.
  Il étend le layout principal 'app.blade.php' et remplit la section 'content'.
  Les données (nom de la commune, statistiques, espèce du mois) sont supposées
  être passées en tant que variables depuis un contrôleur (ex: HomeController).
--}}

@section('content')

{{-- CSS spécifique à la page d'accueil. Peut être dans un fichier .css séparé et importé via Vite. --}}
@push('styles')
<style>
    .hero-section {
        /* L'image de fond est définie ici pour utiliser le helper asset() de Laravel */
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("{{ asset('images/paysage-commune.jpg') }}");
        height: 80vh;
        background-size: cover;
        background-position: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        text-align: center;
        margin-top: -1rem; /* Compense le margin-bottom de la navbar */
    }
    .hero-section h1 {
        font-size: 3.5rem;
        font-weight: bold;
    }
    .hero-section .lead {
        font-size: 1.25rem;
        margin-bottom: 2rem;
    }
    .search-bar {
        width: 100%;
        max-width: 600px;
    }
    .reign-grid, .featured-grid {
        display: grid;
        gap: 1.5rem;
    }
    /* Pour les écrans larges, on passe en colonnes */
    @media (min-width: 768px) {
        .reign-grid {
            grid-template-columns: repeat(3, 1fr);
        }
        .featured-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .card-link {
        text-decoration: none;
        color: inherit;
    }
    .card-link .card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }
    .card-link .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .card-link .card img.icon {
        height: 60px;
        margin-bottom: 1rem;
    }
</style>
@endpush

{{-- BLOC 1: HERO SECTION --}}
<header class="hero-section">
    <div class="container">
        <h1 class="display-3">ATLAS DE LA BIODIVERSITÉ</h1>
        <p class="h3 fw-light">de {{ $communeName ?? 'votre Commune' }}</p>
        <p class="lead mt-3">Explorez les trésors vivants qui vous entourent.</p>

        <form action="" method="GET" class="mt-4 search-bar">
            <div class="input-group input-group-lg">
                <input type="text" class="form-control" name="query" placeholder="Rechercher une espèce, une famille..." aria-label="Rechercher une espèce">
                <button class="btn btn-primary" type="submit" id="button-addon2">Rechercher</button>
            </div>
        </form>
    </div>
</header>

<div class="container my-5">

    {{-- BLOC 2: PORTES D'ENTRÉE TAXONOMIQUES --}}
    <section class="text-center mb-5">
        <h2 class="mb-4">Explorer par grand règne</h2>
        <div class="reign-grid">
            {{-- Carte Faune --}}
            <a href="" class="card-link">
                <div class="card">
                    <img src="{{ asset('icons/fauna.svg') }}" alt="Icône Faune" class="icon">
                    <h3>La Faune</h3>
                    <p>Des mammifères discrets aux insectes colorés, découvrez les animaux de notre territoire.</p>
                    <p class="text-muted fw-bold">{{ $faunaCount ?? '0' }}+ espèces recensées</p>
                </div>
            </a>

            {{-- Carte Flore --}}
            <a href="" class="card-link">
                <div class="card">
                    <img src="{{ asset('icons/flora.svg') }}" alt="Icône Flore" class="icon">
                    <h3>La Flore</h3>
                    <p>Arbres, fleurs sauvages et plantes remarquables qui façonnent nos paysages.</p>
                    <p class="text-muted fw-bold">{{ $floraCount ?? '0' }}+ espèces identifiées</p>
                </div>
            </a>

            {{-- Carte Fonge --}}
            <a href="" class="card-link">
                <div class="card">
                    <img src="{{ asset('icons/fungi.svg') }}" alt="Icône Fonge" class="icon">
                    <h3>Les Champignons</h3>
                    <p>Un monde fascinant et essentiel à nos écosystèmes forestiers.</p>
                    <p class="text-muted fw-bold">{{ $fungiCount ?? '0' }}+ espèces répertoriées</p>
                </div>
            </a>
        </div>
    </section>

    {{-- BLOC 3: MISES EN AVANT DYNAMIQUES --}}
    <section class="p-4 bg-light rounded-3">
        <h2 class="text-center mb-4">À la une en ce moment</h2>
        <div class="featured-grid">
            {{-- Colonne Espèce du mois --}}
            @if(isset($speciesOfTheMonth))
            <article class="d-flex flex-column flex-md-row gap-3 align-items-center">
                <img src="" class="rounded" alt="Photo de {{ $speciesOfTheMonth->name }}" style="width:150px; height:150px; object-fit: cover;">
                <div>
                    <h4>L'espèce du mois : {{ $speciesOfTheMonth->name }}</h4>
                    <p>{{ $speciesOfTheMonth->short_description }}</p>
                    <a href="" class="btn btn-sm btn-outline-primary">Découvrir sa fiche</a>
                </div>
            </article>
            @else
             <div class="text-center text-muted">Pas d'espèce du mois pour le moment.</div>
            @endif

            {{-- Colonne Chiffres clés --}}
            <aside class="d-flex flex-column justify-content-center p-3">
                <h4>La biodiversité en chiffres</h4>
                <ul class="list-unstyled">
                    <li class="mb-2">🐦 <strong>{{ $birdCount ?? '0' }}</strong> espèces d'oiseaux nicheurs ou de passage.</li>
                    <li class="mb-2">🦋 <strong>{{ $butterflyCount ?? '0' }}</strong> espèces de papillons de jour.</li>
                    <li>🛡️ <strong>{{ $protectedCount ?? '0' }}</strong> espèces à statut de protection.</li>
                </ul>
            </aside>
        </div>
    </section>

</div>
@endsection

@push('scripts')
{{-- Si vous avez besoin de JS spécifique pour cette page --}}
@endpush
