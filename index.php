<?php
  require_once('lib.php');
  require_once('site.php');
//////////////////////////////////////////////////////////////////////////////

  $page='about';
	$preq='';

  if (isset($_REQUEST['page']))
    $preq=rawurldecode($_REQUEST['page']);

  if (!empty($preq) && in_array($preq, array_merge(array_keys($pages), array_keys($hidden))))
    $page=$preq;

  if (!empty($preq) && ($preq=='admin' || $preq=='adminschedule' || $preq=='upload')) {
    if (authenticate()) {
      require_once('submit.php');
      $page=$preq;
    } else {
      header('Location: logon.php');
      exit;
    }
  }

  if ($page=='program' || $page=='adminschedule') {
    require_once('programdb.php');
  }

  if (  !empty($preq) && $preq=='registration' 
      && isset($regclosed) && $regclosed) {
	$page='regclosed';
  }

  if (!empty($preq) && $preq=='registration') {
    require_once('submit.php');
    if (checkreg()) 
      if (savereg())
	$page='regcomplete';
  }
 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <title>LAC2011: The Linux Audio Conference</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="static/style.css" type="text/css"/>
  <meta name="Author" content="Robin Gareus" />
  <meta name="description" content="Linux Audio Conference 2011" />
  <meta name="keywords" content="LAC2011, LAC, Linux Audio Conference 2011,Linux, Music, Developer Meeting, Music Department, National University of Ireland, Maynooth" />
  <meta name="google-site-verification" content="MN3y5vmxMxuQpTQhQPoecYv4NXe4Jj8i1zH7hUoIme0" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <script type="text/javascript" src="static/script.js"></script>
</head>
<body>
<div class="envelope main">
  <div id="header">&nbsp;</div>
  <div id="titlebar">
    <div id="maintitle"> Linux Audio Conference 2011 </div>
    <div id="subtitle">The Open Source Music and Sound Conference</div>
		<div id="wherewhen"> May 6-8 2011, Maynooth, Ireland</div>
    <div>LECTURES / WORKSHOPS / EXHIBITION / CONCERTS / CLUBNIGHT</div>
  </div>
  <div id="mainmenu">
<?php
  $i=0;
  foreach($pages as $p => $t) {
    echo '
    <div class="menuitem'.(($page==$p)?' tabactive':'').'" id="tab_'.$i++.'">
        <a href="?page='.$p.'">'.$t.'</a>
    </div>'."\n";
  }
  echo '<span class="menuitem" id="tab_'.$i.'">&nbsp;</span>';
?>
  </div>
  <div id="wrapper">
    <div id="content">
<?php
	if (!in_array($page, $nosponsors)) {
    echo '    <div id="supporterbar">LAC 2011 is supported by<br/><br/>'."\n";
    foreach ($sponsors as $sl => $si) {
      echo '<div><a href="'.$sl.'"'."\n";
      echo '     rel="supporter"><img src="'.$si['img'].'" title="'.$si['title'].'" alt="'.$si['title'].'"/>';
      echo '<br/><span>'.$si['title'].'</span>';
      echo '</a></div>'."\n";
    }
    echo '    </div>'."\n";
  }
?>
<?php
  require_once('pages/'.$page.'.php');
?>
      <div style="clear:both;">&nbsp;</div>
    </div>
  </div>
  <div id="createdby"> Nov 03 2010, Victor Lazzarini, John Lato &amp; Robin Gareus</div>
</div>

<div class="envelope" id="footerwrap">
  <div class="footer">
<a href="http://www2.clustrmaps.com/counter/maps.php?url=http://lac.linuxaudio.org/2011/" id="clustrMapsLink" rel="external"><img src="http://www2.clustrmaps.com/counter/index2.php?url=http://lac.linuxaudio.org/2011/" style="border:0px;" alt="Locations of visitors to this page" title="Locations of visitors to this page" id="clustrMapsImg" />
</a>
<script type="text/javascript">
function cantload() {
img = document.getElementById("clustrMapsImg");
img.onerror = null;
img.src = "http://www.clustrmaps.com/images/clustrmaps-back-soon.jpg";
document.getElementById("clustrMapsLink").href = "http://clustrmaps.com";
}
img = document.getElementById("clustrMapsImg");
img.onerror = cantload;
</script>
  </div>
  <div class="footer">
    <a href="http://validator.w3.org/check?uri=referer" rel="external"><img
        src="img/button-xhtml.png"
        alt="Valid XHTML 1.0 Strict"/></a>
    <a href="http://jigsaw.w3.org/css-validator/check/referer" rel="external"><img
        src="img/button-css.png"
        alt="Valid XHTML 1.0 Strict"/></a>
    <a href="http://www.mozilla.com/en-US/firefox/firefox.html" rel="external"><img
        src="img/button-firefox.png"
        alt="Get Firefox"/></a><br/>
    <p>LINUX&reg; is a <a href="http://www.linuxmark.org/" rel="external">registered trademark</a> of Linus Torvalds in the USA and other countries.<br />Hosting provided by the <a href="http://www.music.vt.edu" rel="external">Virginia Tech Department of Music</a> and <a href="http://disis.music.vt.edu" rel="external">DISIS</a>.<br/>Implementation by <a href="http://rg42.org/" rel="external">RSS</a>.</p>
  </div>
</div> 
</body>
</html>
