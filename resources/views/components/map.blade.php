@props(['observations', 'mapid'])

var observations = {!! json_encode($observations) !!}
var {{ 'map'.$mapid }}=map_index(observations,'{{ $mapid }}');