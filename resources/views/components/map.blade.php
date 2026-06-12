@props(['observations', 'mapid', 'zoomLevel', 'communeCode'])

var observations = {!! json_encode($observations) !!}
var {{ 'map'.$mapid }}=map_index(observations,'{{ $mapid }}', '{{ $communeCode }}', {{ $zoomLevel ?? 12 }});