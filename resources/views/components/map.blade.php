@props(['observations', 'mapid'])

var observations = {!! json_encode($observations) !!}

// Display map
var {{ $mapid }}=map_index(observations,'{{ $mapid }}');
