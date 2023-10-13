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