function update() {
	jQuery(document).ready(($) => {
			let this2 = this;    

			$.post(ajax_obj.ajax_url, {         
				_ajax_nonce: ajax_obj.nonce,
				action: "update_events",
			}, data => {
				console.log(data);
			});
	});
}