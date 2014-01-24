
function obalky_display_thumbnail(element, bibinfo) {
  href = bibinfo["cover_thumbnail_url"];
  if (href != undefined) {
    var img_format = '#' + $(element).attr('id');
    $(img_format).empty();
    $(img_format).prepend("<img align='left' src='" + bibinfo["cover_medium_url"] + "' alt='" + cover_text + "' height='80' width='63'></img>");
  }
}

function obalky_display_cover(element, bibinfo) {
  var href = bibinfo["cover_medium_url"];
  var backlink = bibinfo["backlink_url"];
  if (href == undefined) {
    href = bibinfo["toc_thumbnail_url"];
    backlink = bibinfo["toc_pdf_url"];
  }
  if (href != undefined) {
    var img_format = '#' + $(element).attr('id') + "_format";
    $(img_format).remove();
    $(element).prepend("<a href='" + backlink + "'><img align='left' src='" + href + "' alt='" + cover_text + "'></img></a>");
  }
  toc_url = bibinfo["toc_pdf_url"];
  if (toc_url != undefined) {
    var toc = '#' + $(element).attr('id') + "_toc";
    toc_thumbnail_url = bibinfo["toc_thumbnail_url"];
    $(toc).append("<a href='" + toc_url + "'><img align='left' src='" + toc_thumbnail_url + "' alt='" + cover_text + "'></img></a>");
  }
}

