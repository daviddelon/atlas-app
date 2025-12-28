@extends('layouts.app')



@section('content')
    <div class="container">
        <div class="row">
            @include('tree.vivant')

            @php
                $segments = explode('/', request()->path());
                $category = $segments[1] ?? '';
            @endphp

            @switch($category)
                @case('angiospermes')
                    @include('tree.angiospermes')
                @break

                @default
            @endSwitch

            <main class="col-md-12">
                @foreach ($taxa as $taxon)
                    <div class="mb-4 border rounded-3 p-3 shadow-sm">
                        <div class="row align-items-start">
                            <!-- Colonne image  -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                @if ($taxon->default_photo_url())
                                    <img class="w-100 rounded" style="height: 400px; object-fit: cover;"
                                        alt="{{ $taxon->common_name }}" src="{{ $taxon->default_photo_url() }}">
                                @endif
                                <div class="text-muted small mt-1">
                                    {{ $taxon->photo->author ?? '' }}
                                    {{ $taxon->photo->license ?? '' }}
                                </div>
                            </div>

                            <!-- Colonne texte  -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <h6 class="mb-1"><em>{{ $taxon->scientific_name }}</em></h6>
                                <p class="fw-bold small mb-2">{{ $taxon->common_name }}</p>
                                <div class="small">
                                    @if (auth()->user() && auth()->user()->isAdmin())
                                        <livewire:like :taxon="$taxon" />
                                    @endif
                                    <div>
                                        Période :
                                        @php $period = $taxon->observedPeriod(); @endphp
                                        {{ $period['start'] ?? 'N/A' }} - {{ $period['end'] ?? 'N/A' }}<br>
                                        Nombre d'observations : {{ $taxon->observations_count }}<br>
                                        Nombre d'observateurs : {{ $taxon->observersCount() }}<br>
                                    </div>
                                </div>
                            </div>

                            <!-- Colonne carte -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="w-100 rounded" style="height: 400px;" id="map{{ $taxon->id }}"></div>
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
                 <x-map :observations="$observations" :mapid="$taxon->id"></x-map>
            </script>
        @endpush
    @endforeach

    <div>
        {{ $taxa->links() }}
    </div>
@endsection
