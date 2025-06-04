@props(['observations', 'mapid'])

var observations = {!! json_encode($observations) !!}

// Display map
var {{ 'map'.$mapid }}=map_index(observations,'{{ $mapid }}');
