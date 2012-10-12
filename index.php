<?php
# vim: ts=2 et
  require_once('lib/lib.php');
  require_once('site.php');
//////////////////////////////////////////////////////////////////////////////

  $page=$homepage;
  $preq='';

  if (isset($_REQUEST['page']))
    $preq=rawurldecode($_REQUEST['page']);

  if (!empty($preq) && in_array($preq, array_merge(array_keys($pages), array_keys($hidden))))
    $page=$preq;

  if (!empty($preq) && in_array($preq, $adminpages)) { 
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

  if (!empty($preq) && $preq!=$page && $page==$homepage) {
    header('HTTP/1.0 404 Not Found');
    $page='404';
  }

### BEGIN OUTPUT ###
  xhtmlhead();
?>
<body>
<div class="braille"><a href="#main-content">Skip to content</a></div>
<div id="toprow">
  <div id="titlebar">
  <div id="topline"></div>
    <div id="maintitle">Linux Audio Conference <?=LACY?> </div>
    <div id="subtitle">The Open Source Music and Sound Conference</div>
    <div id="wherewhen"><?=$config['headerlocation']?></div>
    <div>LECTURES / WORKSHOPS / EXHIBITION / CONCERTS / CLUBNIGHTS / RADIO</div>
  </div>
  <div class="blenddown"> </div>
</div>

<div id="payload-layout">
  <div id="logoright"> </div>
  <div id="mainmenu">
<?php
  ### populate menu bar ##
  $i=0;
  foreach($pages as $p => $t) {
    echo '
    <div class="menuitem'.(($page==$p)?' tabactive':'').'">
        <a href="'.local_url($p).'">'.$t.'</a>
    </div>'."\n";
  }
  echo '    <div style="clear:both; height:0px;">&nbsp;</div>'."\n";
?>
  </div>

  <div id="leftcolumn">
    <div id="lefthead"> </div>
<?php 
  if ($page=='admin' || $page=='adminschedule') {
    admin_fieldset(-3);
  } else {
    echo '<div class="center"><a href="https://www.facebook.com/LinuxAudioConference" rel="external"><img src="img/logos/findUsOnFacebook.png" alt="find us on facebook" title="find us on facebook" /></a></div>';
    echo '<div class="center"><a href="https://twitter.com/linuxaudioconf" rel="external"><img src="img/logos/followUsOnTwitter.png" alt="follow us on twitter" title="follow us on twitter" /></a></div>';
    echo '<hr class="psep"/>'."\n";
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
    <a name="main-content"></a>

<?php
  require_once('pages/'.$page.'.php');

  ### content-footer
  #format page mod time
  $mtime_page=filemtime('pages/'.$page.'.php');
  $mtime_idx=0;
  $mtime_skip=false;
  switch($page) {
    case 'sponsors':
      $mtime_idx=filemtime('site.php');
      break;
    case 'program':
    case 'adminschedule':
      $mtime_idx=filemtime(preg_replace('@^[^:]*:@', '', PDOPRGDB));
      break;
    case 'profile':
    case 'speakers':
      $mtime_skip = true;
      break;
    case 'admin':
    case 'participants':
      $mtime_idx=filemtime(preg_replace('@^[^:]*:@', '', PDOREGDB));
      break;
    case 'upload':
    case 'files':
    default:
      $mtime_idx=filemtime('index.php');
  }
  if (!$mtime_skip) {
    $mtime=$mtime_page>$mtime_idx?$mtime_page:$mtime_idx;
    $mdate=date("l, M j Y H:i e", $mtime);
  }
?>

    </div>
  </div>
  <div style="clear:both; height:0px;">&nbsp;</div>
</div>

<div id="footerwrap">
  <div class="blendup"> </div>

<?php
  if (!$mtime_skip)
    echo '<div id="createdby">Last modified: '.$mdate.' - IOhannes Zm&ouml;lnig &amp; Robin Gareus</div>';
  else 
    echo '<div id="createdby"><br/></div>';
?>
  <div class="blenddown"> </div>

  <a href="http://validator.w3.org/check?uri=referer" rel="external"><img
      src="img/button-xhtml.png"
      alt="Valid XHTML 1.0 Strict"/></a>
  <a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3" rel="external"><img
      src="img/button-css.png"
      alt="Valid XHTML 1.0 Strict"/></a>
  <a href="http://www.mozilla.com/en-US/firefox/firefox.html" rel="external"><img
      src="img/button-firefox.png"
      alt="Get Firefox"/></a><br/>
  <p>LINUX<sup>&reg;</sup> is a <a href="http://www.linuxmark.org/" rel="external">registered trademark</a> of Linus Torvalds in the USA and other countries.<br />Hosting provided by the <a href="http://www.music.vt.edu" rel="external">Virginia Tech Department of Music</a> and <a href="http://disis.music.vt.edu" rel="external">DISIS</a>.<br/>Design and implementation by <a href="http://rg42.org/" rel="external">RSS</a>.</p>
  <div class="blendup"> </div>
</div> 
</body>
</html>
