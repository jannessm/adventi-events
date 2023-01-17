const waitFor = delay => new Promise(resolve => setTimeout(resolve, delay));
let ad_ev_map;
let ad_ev_map_marker = [];
let ad_ev_address_hints = [];
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
  if (!!leaflet_options.location_lng && !!leaflet_options.location_lat) {
		const lng = parseFloat(leaflet_options.location_lng);
		const lat = parseFloat(leaflet_options.location_lat);
		updateLocation({lng, lat});
	}
}

function updateLocation(point) {
	//remove old marker
	ad_ev_map_marker.forEach(m => {
		ad_ev_map.removeLayer(m);
	});
	ad_ev_map_marker = [];
	
	const marker = L.marker(point).bindPopup(leaflet_options.location);
	
	ad_ev_map_marker.push(marker);
	
	ad_ev_map.setView(point, 17);
	marker.addTo(ad_ev_map);
}

jQuery(document).ready(async (_$) => {
	try {
		if (!leaflet_options) return;
	} catch {
		return;
	}

	$ = _$;
	ad_ev_map = await init_map();

	init_location();
});