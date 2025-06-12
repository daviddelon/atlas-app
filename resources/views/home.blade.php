@extends('layouts.app')



@section('content')



<div class="container">
    @foreach ($taxa as $taxon)
        <div class="taxon-card">
            <div class="row align-items-start">
                <div class="col-md-3 mb-3 mb-md-0">
                    @if ($taxon->default_photo_url())
                        <img  class="taxon-img" alt="{{ $taxon->common_name }}" src={{ $taxon->default_photo_url() }}></img>
                    @endif
                    <div class="text-muted small mt-1">
                        {{ $taxon->photo->author ?? ""}}
                        {{ $taxon->photo->license ?? ""}}
                    </div>
                </div>
                <div class="col-md-5 mb-3 mb-md-0">
                    <h5><em>{{ $taxon->scientific_name }}</em> L.</h5>
                    <p><strong>{{ $taxon->common_name }}</strong></p>
                    <p>
                    Description
                    </p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="taxon-map" id="map{{ $loop->index }}"></div>
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
            <x-map :observations="$observations" :mapid="$loop->index"></x-map>
        </script>
    @endpush

@endforeach

<div>
    {{ $taxa->links() }}
</div>


@endsection
