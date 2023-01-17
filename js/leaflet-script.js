const waitFor = delay => new Promise(resolve => setTimeout(resolve, delay));
let ad_ev_change_delay;
let ad_ev_map;
let ad_ev_map_marker = [];
let ad_ev_address_hints = [];
let $;

async function init_map() {
	await waitFor(1);

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
	// if location string without point available
	if (!!leaflet_options.location && (!leaflet_options.location_lng || !leaflet_options.location_lat)) {
		searchLocation(leaflet_options.location);
	
	// if point is available extract long, lat from input
	} else if (!!leaflet_options.location_lng && !!leaflet_options.location_lat) {
		const lng = parseFloat(leaflet_options.location_lng);
		const lat = parseFloat(leaflet_options.location_lat);
		updateLocation({lng, lat});
	}
}

function add_input_change_listener() {
	if (!!leaflet_options.input_id) {
		$('#' + leaflet_options.input_id).on('input', e => {
			if (!!ad_ev_change_delay) {
				clearTimeout(ad_ev_change_delay);
			}
			ad_ev_change_delay = setTimeout(() => {
				searchLocation(e.target.value);
			}, 1000);
		});
	}
}

function searchLocation(location) {
	if (!leaflet_options.graphhopper_api_key) {
		return;
	}

	$.get("https://graphhopper.com/api/1/geocode?q=" + encodeURIComponent(location) + "&locale=de&key=" + leaflet_options.graphhopper_api_key, (res) => {
		if (res.hits.length > 0) {
			ad_ev_address_hints = res.hits;

			if (!!leaflet_options.input_proposals_id) {
				const container = document.querySelector('#' + leaflet_options.input_proposals_id);
				clearProposals();
				
				if (res.hits.length > 1) {
					ad_ev_address_hints.forEach((hit, i) => {
						container.style.display = 'inline-block';
						const option = document.createElement('div');
						option.innerText = getAddress(hit);
						option.className = 'ad_ev_location_proposal';
						$(option).click(() => selectHint(i));
						container.appendChild(option);
					});
				} else if (res.hits.length == 1) {
					$('#' + leaflet_options.input_id).val(getAddress(res.hits[0]));
					updateLocation(res.hits[0].point);
				}
			}
		}
	});
}

function selectHint(i) {
	clearProposals();
	updateLocation(ad_ev_address_hints[i].point);
	$('#' + leaflet_options.input_id).val(getAddress(ad_ev_address_hints[i]));
	ad_ev_address_hints = [];
}

function clearProposals() {
	$('#' + leaflet_options.input_proposals_id).empty().css({'display': 'none'});

}

function updateLocation(point) {
	//remove old marker
	ad_ev_map_marker.forEach(m => {
		ad_ev_map.removeLayer(m);
	});
	ad_ev_map_marker = [];
	
	const marker = L.marker(point).bindPopup(leaflet_options.location);

	if (!!leaflet_options.input_lng && !!leaflet_options.input_lat) {
		$('#' + leaflet_options.input_lng).val(point.lng);
		$('#' + leaflet_options.input_lat).val(point.lat);
	}
	
	ad_ev_map_marker.push(marker);
	
	ad_ev_map.setView(point, 17);
	marker.addTo(ad_ev_map);
}

function getAddress(hit) {
	let addr = '';

	if (!!hit.street) {
		addr += hit.street;
	} else if (!!hit.name) {
		addr += hit.name;
	}

	if (!!hit.housenumber) {
		if (addr.length > 0) {
			addr += ' ';
		}
		addr += hit.housenumber;
	}

	if (!!hit.postcode) {
		if (addr.length > 0) {
			addr += ', ';
		}
		addr += hit.postcode;
	}

	if (!!hit.city) {
		if (addr.length > 0 && !!hit.postcode) {
			addr += ' ';
		} else if (addr.length > 0) {
			addr += ', ';
		}
		addr += hit.city;
	}

	return addr;
}

jQuery(document).ready(async (_$) => {
	$ = _$;
	ad_ev_map = await init_map();

	init_location();

	add_input_change_listener();
});