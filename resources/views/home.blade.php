@extends('layouts.app')



@section('content')
<div class="container">
    <div class="row">

        @foreach ($taxa as $taxon)
        <div class="card" style="width: 18rem;">
            <div style="height: 18rem;" id="map{{ $loop->index }}"></div>
            <div class="card-body">
                <h5 class="card-title">{{ $taxon->common_name }}</h5>
                <p class="card-text">Texte</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
        @endforeach
    </div>
</div>


@foreach ($taxa as $taxon)

    @php
    $observations=$taxon->observations->map(function ($observation) {
    return  array ( 'latlng' => array($observation->latitude,$observation->longitude ));
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
