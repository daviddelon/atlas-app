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
                @case('gymnospermes')
                @case('fougeres')
                @case('mousses')
                    @include('tree.familles', ['categories' => $categories])
                @break

                @default
            @endSwitch

             <main class="col-md-12">
                 @if(isset($family) && $family)
                     <div class="mb-4">
                         <h2 class="text-center">
                             Famille : {{ ucfirst($family) }}
                         </h2>
                     </div>
                 @endif

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
                                    {!! $taxon->description?->getFormattedContent() ?? '' !!}
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









    <div>
        {{ $taxa->links() }}
    </div>
@endsection
