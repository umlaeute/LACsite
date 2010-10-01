function externalLinks() {
  if (!document.getElementsByTagName) return;
  var anchors = document.getElementsByTagName("a");
  for (var i=0; i<anchors.length; i++) {
    var anchor = anchors[i];
    if (anchor.getAttribute("href") &&
        anchor.getAttribute("rel") == "external")
      anchor.target = "_blank";
    if (anchor.getAttribute("href") &&
        anchor.getAttribute("rel") == "wiki")
      anchor.target = "lac2011-wiki";
    if (anchor.getAttribute("href") &&
        anchor.getAttribute("rel") == "supporter")
      anchor.target = "lac2011-sponsor";
    if (anchor.getAttribute("href") &&
        anchor.getAttribute("rel") == "registration")
      anchor.target = "lac2011-registration";
  }
}
window.onload = externalLinks;

function inlineInfoBox(id){
  document.getElementById('infobox').style.display = "inline";
  document.getElementById('dimmer').style.display = "inline";
  document.getElementById('infobox').scrollTop=0; /* FIXME: scrollTop is not compatible */
}

function showInfoBox(id){
  if (document.getElementById('ieframe')) {
    document.getElementById('ieframe').src  = 'raw.php?pdb_filterid='+id;
  } else {
    document.getElementById('infoframe').data = 'raw.php?pdb_filterid='+id;
  }
  inlineInfoBox();
}

function showInfoPix(id){
  if (document.getElementById('ieframe')) {
    document.getElementById('ieframe').src  = 'pix.php?id='+id;
  } else {
    document.getElementById('infoframe').data = 'pix.php?id='+id;
  }
  inlineInfoBox();
}
function hideInfoBox() {
  if (document.getElementById('ieframe')) {
    document.getElementById('ieframe').src  = 'raw.php';
  } else {
    document.getElementById('infoframe').data = 'raw.php';
  }
  document.getElementById('infobox').style.display = "none";
  document.getElementById('dimmer').style.display = "none";
}
