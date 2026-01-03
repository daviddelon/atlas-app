@props(['observations', 'mapid', 'zoomLevel'])

var observations = {!! json_encode($observations) !!}
var {{ 'map'.$mapid }}=map_index(observations,'{{ $mapid }}', '{{ session("current_commune_code") }}', {{ $zoomLevel ?? 12 }});