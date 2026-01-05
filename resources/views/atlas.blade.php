@extends('layouts.app')



@section('content')
    <div class="container">
        <div class="row">
            @php
                $segments = explode('/', request()->path());
                $category = $segments[1] ?? '';
            @endphp

            @switch($category)
                @case('angiospermes')
                    @include('tree.angiospermes', ['categories' => $categories])
                @break

                @default
            @endSwitch

            <main class="col-md-12">
                @foreach ($taxa as $taxon)
                    <div class="mb-4 border rounded-3 p-3 shadow-sm">
                        <div class="row align-items-start">
                            <!-- Colonne gauche : nom + photo -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <p class="fw-bold mb-1" style="font-size: 1.3em; color: #2d5a27;">{{ ucfirst($taxon->common_name) }}</p>
                                <h6 class="mb-3 text-muted"><em>{{ $taxon->scientific_name }}</em></h6>
                                @if ($taxon->default_photo_url())
                                    <img class="w-100 rounded shadow" style="height: 400px; object-fit: cover;"
                                        alt="{{ $taxon->common_name }}" src="{{ $taxon->default_photo_url() }}">
                                @endif
                                <div class="text-muted small mt-1">
                                    {{ $taxon->photo->author ?? '' }}
                                    {{ $taxon->photo->license ?? '' }}
                                </div>
                            </div>

                            <!-- Colonne milieu : description -->
                            <div class="col-md-4 mb-3 mb-md-0" style="padding-top: 70px;">
                                @if (auth()->user() && auth()->user()->isAdmin())
                                    <livewire:like :taxon="$taxon" />
                                @endif
                                <div>
                                    {!! $taxon->description->getFormattedContent() ?? '' !!}
                                </div>
                            </div>

                            <!-- Colonne droite : carte -->
                            <div class="col-md-4 mb-3 mb-md-0" style="padding-top: 10px;">
                                <div class="w-100 rounded" style="height: 500px;" id="map{{ $taxon->id }}"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </main>
        </div>
    </div>



    @foreach ($taxa as $taxon)
        @php
            $observations = $taxon->observations->map(function ($observation) {
                return [$observation->latitude, $observation->longitude, $observation->quality];
            });
        @endphp
        @push('js')
            <script type="module">
                var observations = {!! json_encode($observations) !!}
                window['map{{ $taxon->id }}'] = map_index(observations, '{{ $taxon->id }}', '{{ session("current_commune_code") }}', {{ $zoomLevel }});
            </script>
        @endpush
    @endforeach

    @push('js')
        <script type="module">
            document.addEventListener('DOMContentLoaded', function() {
                const familySelectors = document.querySelectorAll('.family-selector');

                familySelectors.forEach(button => {
                    button.addEventListener('click', function() {
                        const slug = this.dataset.slug;

                        // Charger les taxons via AJAX
                        fetch(`/plantes/angiospermes/family/${slug}`)
                            .then(response => response.json())
                            .then(data => {
                                // Changer l'URL pour refléter la famille sélectionnée
                                history.pushState(null, '', `/plantes/angiospermes/${slug}`);

                                // Mettre à jour la liste des taxons
                                const taxaContainer = document.querySelector('main.col-md-12');
                                taxaContainer.innerHTML = '';

                                data.taxa.forEach(taxon => {
                                    const photoUrl = taxon.photo_url;
                                    const taxonHtml = `
                                        <div class="mb-4 border rounded-3 p-3 shadow-sm">
                                            <div class="row align-items-start">
                                                <div class="col-md-4 mb-3 mb-md-0">
                                                    <p class="fw-bold mb-1" style="font-size: 1.3em; color: #2d5a27;">${taxon.common_name || ''}</p>
                                                    <h6 class="mb-3 text-muted"><em>${taxon.scientific_name}</em></h6>
                                                    ${photoUrl ? `<img class="w-100 rounded shadow" style="height: 400px; object-fit: cover;" alt="${taxon.common_name || ''}" src="${photoUrl}">` : ''}
                                                    ${taxon.photo ? `<div class="text-muted small mt-1">${taxon.photo.author || ''} ${taxon.photo.license || ''}</div>` : ''}
                                                </div>
                                                <div class="col-md-4 mb-3 mb-md-0" style="padding-top: 70px;">
                                                    <div>${taxon.formatted_description}</div>
                                                </div>
                                                <div class="col-md-4 mb-3 mb-md-0" style="padding-top: 10px;">
                                                    <div class="w-100 rounded" style="height: 500px;" id="map${taxon.id}"></div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    taxaContainer.insertAdjacentHTML('beforeend', taxonHtml);

                                    // Charger la carte pour ce taxon
                                    const observations = taxon.observations.map(obs => [obs.latitude, obs.longitude, obs.quality]);
                                    window['map' + taxon.id] = map_index(observations, taxon.id, '{{ session("current_commune_code") }}', {{ $zoomLevel }});
                                });

                                // Mettre à jour la pagination
                                const paginationContainer = document.querySelector('main + div');
                                if (paginationContainer) {
                                    paginationContainer.innerHTML = data.pagination;
                                }
                            })
                            .catch(error => console.error('Erreur lors du chargement:', error));
                    });
                });
            });
        </script>
    @endpush



    <div>
        {{ $taxa->links() }}
    </div>
@endsection
