@extends('layouts.app')

@section('content')


 <div class="taxon-map" id="map0"></div>

@php
$obs=$observations->map(function ($observation) {
    return  array($observation->latitude,$observation->longitude );
});

@endphp
@push('js')
    <script type="module">
        <x-map :observations="$obs" :mapid="0"></x-map>
    </script>
@endpush


@endsection
