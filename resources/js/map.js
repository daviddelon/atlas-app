// Display a map with markers and return a map

export function map_index(markers,mapid) {

    var map = L.map(mapid, {
        center: [43.78, 3.76],
        zoom: 13,
        scrollWheelZoom: false
    });

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        scrollWheelZoom: false,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);


    return map;

}
