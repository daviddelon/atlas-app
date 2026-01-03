import './leaflet.snogylop.js';

// Display a map with markers and return a map

export function map_index(observations,mapid,communeCode) {

    var map = L.map("map"+mapid, {
        scrollWheelZoom: false,
        zoomControl: false,
        zoomSnap: 0.1
    });



    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        scrollWheelZoom: false,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

     // Charger le GeoJSON Contour de Commune
     fetch('/storage/' + communeCode + '.geojson')
        .then(response => response.json())
        .then(data => {
            var layer = L.geoJSON(data, {
                style: {
                    color: "#f8fafc",
                    weight: 2,
                    fillOpacity: 1
                },
                invert: true
            }).addTo(map);
            map.fitBounds(layer.getBounds(), { padding: [50, 50] });
            map.setZoom(12.5);
        });


    var color;
    observations.forEach(function (item) {

        color = (item[2] == 'R') ? '#228B22' : '#FFA500';
        L.circleMarker([item[0], item[1]], {
            radius: 5,
            color: color,
            fillColor: color,
            fillOpacity: 0.8,
            weight: 1
        }).addTo(map);

    });

    return map;

}
