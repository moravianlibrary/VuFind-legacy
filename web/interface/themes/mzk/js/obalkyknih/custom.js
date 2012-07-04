
function obalky_display_thumbnail(element, bibinfo) {
  href = bibinfo["cover_thumbnail_url"];
  if (href != undefined) {
    var img_format = '#' + $(element).attr('id') + "_format";
    $(img_format).remove();
    $(element).prepend("<a href='" + bibinfo["backlink_url"] + "'><img align='left' src='" + bibinfo["cover_medium_url"] + "' alt='" + cover_text + "' height='80' width='63'></img></a>");
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
  toc = bibinfo["toc_pdf_url"];
  if (toc != undefined) {
    $("#bibliographic_details").append("<tr valign='top'><th>"+ content_text + ":</th><td><a href='" + toc + "'</td>obalkyknih.cz</tr>");
  }
}

