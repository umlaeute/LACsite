<?php
  require_once('lib/lib.php');
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
      require_once('lib/submit.php');
      $page=$preq;
    } else {
      header('Location: logon.php');
      exit;
    }
  }

  if ($page=='program' || $page=='adminschedule') {
    require_once('lib/programdb.php');
  }

  if (  !empty($preq) && $preq=='registration' 
      && isset($config['regclosed']) && $config['regclosed']) {
    $page='regclosed';
  }

  if (!empty($preq) && $preq=='registration') {
    require_once('lib/submit.php');
    if (checkreg()) 
      if (savereg())
        $page='regcomplete';
  }

  if (!empty($preq) && $preq!=$page && $page=='about') {
    header(404, "File not found");
    $page='404';
  }
  xhtmlhead();
?>
<body>
<div id="toprow">
  <div id="topline"></div>
  <div id="titlebar">
    <div id="maintitle"> Linux Audio Conference <?=LACY?> </div>
    <div id="subtitle">The Open Source Music and Sound Conference</div>
    <div id="wherewhen">April 12-15 2012, Stanford, Ca, USA</div>
    <div>LECTURES / WORKSHOPS / EXHIBITION / CONCERTS / CLUBNIGHT</div>
  </div>
  <div id="titleend"> </div>
</div>

<div id="payload-layout">
	<div id="logoright"> </div>
  <div id="mainmenu">
<?php
  $i=0;
  foreach($pages as $p => $t) {
    echo '
    <div class="menuitem'.(($page==$p)?' tabactive':'').'">
        <a href="'.local_url($p).'">'.$t.'</a>
    </div>'."\n";
  }
  echo '<div style="clear:both; height:0px;">&nbsp;</div>'."\n";
?>
  </div>
  <div id="leftcolumn">
    <div id="lefthead"> </div>
<?php 
	if ($page=='admin' || $page=='adminschedule') {
		admin_fieldset(-3);
	} else {
    leftbar(); 
    if (function_exists('clustermap')) clustermap(); 
	}
?>
    <p>&nbsp;</p>
    <div class="lbfootl">&nbsp;</div>
    <div class="lbfootr">&nbsp;</div>
  </div>

  <div id="main">
    <div id="content" class="mainheadl">
<?php
require_once('pages/'.$page.'.php');
?>
    </div>
    <div id="mainfootl"> </div>
    <div id="createdby"> Oct 06 2011, Fernando Lopez-Lezcano, Bruno Ruviaro &amp; Robin Gareus</div>
  </div>
  <div style="clear:both; height:0px;">&nbsp;</div>
</div>

<div id="footerwrap">
  <a href="http://validator.w3.org/check?uri=referer" rel="external"><img
      src="img/button-xhtml.png"
      alt="Valid XHTML 1.0 Strict"/></a>
  <a href="http://jigsaw.w3.org/css-validator/check/referer" rel="external"><img
      src="img/button-css.png"
      alt="Valid XHTML 1.0 Strict"/></a>
  <a href="http://www.mozilla.com/en-US/firefox/firefox.html" rel="external"><img
      src="img/button-firefox.png"
      alt="Get Firefox"/></a><br/>
  <p>LINUX<sup>&reg;</sup> is a <a href="http://www.linuxmark.org/" rel="external">registered trademark</a> of Linus Torvalds in the USA and other countries.<br />Hosting provided by the <a href="http://www.music.vt.edu" rel="external">Virginia Tech Department of Music</a> and <a href="http://disis.music.vt.edu" rel="external">DISIS</a>.<br/>Design and implementation by <a href="http://rg42.org/" rel="external">RSS</a>.</p>
</div> 
</body>
</html>
