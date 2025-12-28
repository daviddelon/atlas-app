// Display a map with markers and return a map

export function map_index(observations,mapid) {

    var map = L.map("map"+mapid, {
        zoom: 11,
        scrollWheelZoom: false
    });



    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        scrollWheelZoom: false,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

     // Charger le GeoJSON Contour de Commune
    fetch('/storage/34343.geojson')
        .then(response => response.json())
        .then(data => {
            var layer = L.geoJSON(data, {
                style: {
                    color: "#4A90E2",
                    weight: 2,
                    fillOpacity: 0.1
                }
            }).addTo(map);
            map.fitBounds(layer.getBounds());
        });


    var color;
    observations.forEach(function (item) {

        color = (item[2] == 'R') ? '#228B22' : '#FFA500';
        L.circleMarker([item[0], item[1]], { radius: 2, color: color }).addTo(map);

    });

    return map;

}
