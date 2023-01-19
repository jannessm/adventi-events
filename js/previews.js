jQuery(document).ready($ => {
  setTimeout(() => {
    $('.ad_event').each((i, element) => {
      const el = $(element);
      const d = $(element.querySelector('.ad_event_date'));
      const im = document.createElement('img');
      let im_url = el.css('background-image');
      im_url = im_url.replace('url(','').replace(')','').replace(/\"/gi, "");
      im.src = im_url;
  
      d.css('color', '#' + contrastingColor(getAverageRGB(im)))
    });
    
    // const el = $('#header');
    // const im = document.createElement('img');
    // let im_url = el.css('background-image');
    // im_url = im_url.replace('url(','').replace(')','').replace(/\"/gi, "");
    // im.src = im_url;
  
    // el.css('color', '#' + contrastingColor(getAverageRGB(im)));
  }, 1)
});

function contrastingColor(color)
{
    return (luma(color) >= 165) ? '000' : 'fff';
}
function luma(color) // color can be a hx string or an array of RGB values 0-255
{
    return (0.2126 * color.r) + (0.7152 * color.g) + (0.0722 * color.b); // SMPTE C, Rec. 709 weightings
}

function getAverageRGB(imgEl) {

  var blockSize = 5, // only visit every 5 pixels
      defaultRGB = {r:0,g:0,b:0}, // for non-supporting envs
      canvas = document.createElement('canvas'),
      context = canvas.getContext && canvas.getContext('2d'),
      data, width, height,
      i = -4,
      length,
      rgb = {r:0,g:0,b:0},
      count = 0;

  if (!context) {
      return defaultRGB;
  }

  height = canvas.height = (imgEl.naturalHeight || imgEl.offsetHeight || imgEl.height) * 0.5;
  width = canvas.width = (imgEl.naturalWidth || imgEl.offsetWidth || imgEl.width) * 0.5;

  context.drawImage(imgEl, 0, 0);

  try {
      data = context.getImageData(0, 0, width, height);
  } catch(e) {
      /* security error, img on diff domain */
      return defaultRGB;
  }

  length = data.data.length;

  while ( (i += blockSize * 4) < length ) {
      ++count;
      rgb.r += data.data[i];
      rgb.g += data.data[i+1];
      rgb.b += data.data[i+2];
  }

  // ~~ used to floor values
  rgb.r = ~~(rgb.r/count);
  rgb.g = ~~(rgb.g/count);
  rgb.b = ~~(rgb.b/count);

  return rgb;

}