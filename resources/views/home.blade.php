@extends('layouts.app')



@section('content')


<div class="container">
    @foreach ($taxa as $taxon)
        <div class="mb-4 border rounded-3 p-3 shadow-sm">
            <div class="row align-items-start">
                <!-- Colonne image (agrandie) -->
                <div class="col-md-4 mb-3 mb-md-0">
                    @if ($taxon->default_photo_url())
                        <img class="w-100 rounded"
                             style="height: 400px; object-fit: cover;"
                             alt="{{ $taxon->common_name }}"
                             src="{{ $taxon->default_photo_url() }}">
                    @endif
                    <div class="text-muted small mt-1">
                        {{ $taxon->photo->author ?? '' }}
                        {{ $taxon->photo->license ?? '' }}
                    </div>
                </div>

                <!-- Colonne texte  -->
                <div class="col-md-3 mb-3 mb-md-0">
                    <h6 class="mb-1"><em>{{ $taxon->scientific_name }}</em></h6>
                    <p class="fw-bold small mb-2">{{ $taxon->common_name }}</p>
                    <div class="small">
                        {{  auth()->user() }}
                        @if(auth()->user() && auth()->user()->isAdmin())
                            <livewire:like :taxon="$taxon" />
                        @endif

                    </div>
                </div>

                <!-- Colonne carte -->
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="w-100 rounded"
                         style="height: 400px;"
                         id="map{{ $taxon->id }}"></div>
                </div>
            </div>
        </div>
    @endforeach
</div>



@foreach ($taxa as $taxon)

    @php
    $observations=$taxon->observations->map(function ($observation) {
        return  array($observation->latitude,$observation->longitude,$observation->quality);
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
