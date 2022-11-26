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
					$('#adventi-events-dates').html(Object.keys(data).reduce((d, date) => d + '<span style="display: inline-block;width: 300px">' + date + '</span><span style="display: inline-block">' + data[date] + '</span><br>', ''));
				}
			});
	});
}