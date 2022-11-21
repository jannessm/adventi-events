const waitFor = delay => new Promise(resolve => setTimeout(resolve, delay));
let adventi_events_map;
let adventi_events_map_marker = [];
let adventi_events_address_hints = [];
let $;

async function init_map() {
	await waitFor(0.1);

	map = L.map(leaflet_options.map_id);

	// add the OpenStreetMap tiles
	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
	}).addTo(map);

	// show the scale bar on the lower left corner
	L.control.scale({imperial: true, metric: true}).addTo(map);

	return map;
}

function init_location() {
  if (!!leaflet_options.location_point) {
		const loc_str = leaflet_options.location_point;
		const sep_pos =  loc_str.indexOf(',');
		const lng = parseFloat(loc_str.substring(1,sep_pos));
		const lat = parseFloat(loc_str.substring(sep_pos + 1, loc_str.length-1));
		updateLocation({lng, lat});
	}
}

function updateLocation(point) {
	//remove old marker
	adventi_events_map_marker.forEach(m => {
		adventi_events_map.removeLayer(m);
	});
	adventi_events_map_marker = [];
	
	const marker = L.marker(point).bindPopup(leaflet_options.location);
	
	adventi_events_map_marker.push(marker);
	
	adventi_events_map.setView(point, 17);
	marker.addTo(adventi_events_map);
}

jQuery(document).ready(async (_$) => {
	$ = _$;
	adventi_events_map = await init_map();

	init_location();
});