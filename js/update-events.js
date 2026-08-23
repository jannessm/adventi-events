function update(e) {
	jQuery(document).ready(($) => {
			$('#adventi-events-dates').html('Loading...');
			$.post(ajax_obj.ajax_url, {         
				_ajax_nonce: ajax_obj.nonce,
				action: "update_events",
			}, data => {
				if (typeof(data) === 'string') {
					$('#adventi-events-dates').text(data)
				} else {
					let report = 'Added:<br>';
					
					if (data['added'].length == 0) {
						report += '  Keine Änderungen<br>';
					} else {
						report += Object.keys(data['added']).reduce((d, date) => d + '<span style="display: inline-block;width: 300px">' + date + '</span><span style="display: inline-block">' + data[date] + '</span><br>', '');
					}
					
					report += 'Modified:<br>';
					
					if (data['updated'].length == 0) {
						report += '  Keine Änderungen';
					} else {
						report += Object.keys(data['updated']).reduce((d, date) => d + '<span style="display: inline-block;width: 300px">' + date + '</span><span style="display: inline-block">' + data[date] + '</span><br>', '');
					}
					
					$('#adventi-events-dates').html(report);
				}
			});
	});
}

function delete_all_services(e) {
	jQuery(document).ready(($) => {
			$('#adventi-events-dates').html('Loading...');
			$.post(ajax_obj.ajax_url, {         
				_ajax_nonce: ajax_obj.nonce,
				action: "delete_events",
			}, data => {
				if (typeof(data) === 'string') {
					$('#adventi-events-dates').text(data)
				} else {
					$('#adventi-events-dates').html(data.reduce((d, event) => d + '<span style="display: inline-block;width: 300px">' + event['date']['date'] + '</span><span style="display: inline-block">' + '</span><br>', ''));
				}
			});
	});
}

function import_csv_events(e) {
	jQuery(document).ready(($) => {
		const fileInput = document.getElementById('ad-ev-csv-file');
		if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
			$('#adventi-events-dates').text('Bitte eine CSV Datei auswählen.');
			return;
		}

		const formData = new FormData();
		formData.append('action', 'import_events_csv');
		formData.append('_ajax_nonce', ajax_obj.nonce);
		formData.append('csv_file', fileInput.files[0]);

		$('#adventi-events-dates').html('Loading...');
		$.ajax({
			url: ajax_obj.ajax_url,
			type: 'POST',
			data: formData,
			contentType: false,
			processData: false,
			success: (data) => {
				if (typeof(data) === 'string') {
					$('#adventi-events-dates').text(data);
					return;
				}

				let report = 'Added:<br>';
				if (Object.keys(data['added'] || {}).length === 0) {
					report += '  Keine Änderungen<br>';
				} else {
					report += Object.keys(data['added']).reduce((d, date) => d + '<span style="display: inline-block;width: 300px">' + date + '</span><span style="display: inline-block">' + data['added'][date] + '</span><br>', '');
				}

				report += 'Modified:<br>';
				if (Object.keys(data['updated'] || {}).length === 0) {
					report += '  Keine Änderungen';
				} else {
					report += Object.keys(data['updated']).reduce((d, date) => d + '<span style="display: inline-block;width: 300px">' + date + '</span><span style="display: inline-block">' + data['updated'][date] + '</span><br>', '');
				}
				$('#adventi-events-dates').html(report);
			},
			error: (xhr) => {
				$('#adventi-events-dates').text(xhr.responseText || 'CSV Import fehlgeschlagen.');
			}
		});
	});
}

function copy_api_secret(e) {
	const secretInput = document.getElementById('ad_ev_field_api_secret');
	if (!secretInput) {
		return;
	}

	secretInput.select();
	secretInput.setSelectionRange(0, 99999);
	navigator.clipboard.writeText(secretInput.value);
}