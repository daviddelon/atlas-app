@extends('layouts.app')



@section('content')
<div class="container">
    <div class="row">
        <div class="card" style="width: 18rem;">
            <div style="height: 18rem;" id="map0"></div>
            <div class="card-body">
                <h5 class="card-title">Card title</h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
        <div class="card" style="width: 18rem;">
            <div style="height: 18rem;" id="map1"></div>
            <div class="card-body">
                <h5 class="card-title">Card title</h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
        <div class="card" style="width: 18rem;">
            <div style="height: 18rem;" id="map2"></div>
            <div class="card-body">
                <h5 class="card-title">Card title</h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
    </div>
</div>

@php

 $mapids=array("map0","map1","map2");
@endphp

@foreach ( $mapids as $mapid )

    @push('js')
        <script type="module">
            <x-map :observations="$observations" :mapid="$mapid"></x-map>
        </script>
    @endpush

@endforeach

@endsection
