jQuery(document).ready($ => {
  let firstClick = false;

  $($('#ad_ev_zoom_details').parent().get(0).querySelector('.h-captcha')).hide();

  $('#ad_ev_zoom_details').click(e => {
    e.preventDefault();
    $('#ad_ev_details').html('');

    const sibling = $(e.target.parentNode.querySelector('.h-captcha iframe'));

    if (!firstClick) {
      sibling.parent().show();
      firstClick = true;
      return;
    }

    $.post(zoom_details.ajax_url, {
      action: "zoom_details",
      post_id: zoom_details.post_id,
      h_response: sibling.data('hcaptcha-response')
    }, data => {
      if (!!data.err) {
        $('#ad_ev_details').html(data.err);
      }

      let html = '';

      if (!!data.pwd) {
        html += _ad_ev_label_value('Passwort', data.pwd, zoom_details.label);
      }

      if (!!data.tel) {
        html += _ad_ev_label_value('Tel', data.tel, zoom_details.label);
      }

      if (!!data.link) {
        html += _ad_ev_label_value('', `<button><a href='${data.link}' target="_blank">Beitreten</a></button>`, false);
      }

      $('#ad_ev_details').html(html);
      $('#ad_ev_zoom_details').hide();
    });
  })
});

function _ad_ev_label_value(label, value, add_label) {
  let o = '<p>';

  if (add_label) {
      o += `<b style="width: 100px; display: inline-block">${label}:</b> `;
  }
  
  return o + value + '</p>';
}