jQuery($ => {
  $('#is_real').click(() => {
    update_form();
  });

  $('#is_zoom').click(() => {
    update_form();
  });

  function update_form() {
    if ($('#is_real').prop('checked')) {
      $('#location_input').prop('disabled', false);
      $('#location_lng').prop('disabled', false);
      $('#location_lat').prop('disabled', false);
    } else {
      $('#location_input').prop('disabled', true);
      $('#location_lng').prop('disabled', true);
      $('#location_lat').prop('disabled', true);
    }

    if ($('#is_zoom').prop('checked')) {
      $('#zoom_id').prop('disabled', false);
      $('#zoom_pwd').prop('disabled', false);
      $('#zoom_link').prop('disabled', false);
      $('#zoom_tel').prop('disabled', false);
    } else {
      $('#zoom_id').prop('disabled', true);
      $('#zoom_pwd').prop('disabled', true);
      $('#zoom_link').prop('disabled', true);
      $('#zoom_tel').prop('disabled', true);
    }
  }

  update_form();
});