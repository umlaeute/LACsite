<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <title>LAC Video Player</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="static/vstyle.css" type="text/css"/>
  <meta name="Author" content="Robin Gareus" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <script type="text/javascript" src="static/script.js"></script>
</head>
<body>
<div class="container">
<?php
#require_once('config.php');
require_once('lib.php');
require_once('programdb.php');
function printerror($msg) {
  echo '<div class="error">ERROR: '.$msg.'</div></div></body></html>';
  echo '<div class="footer">Back to <a href="index.php?page=program">conference site</a>.</div>';
  exit;
}
if (!isset($_REQUEST['id']))
  printerror('invalid request - no id specified.');

$id=intval(rawurldecode($_REQUEST['id']));
$q='SELECT * FROM activity WHERE id='.$id.';';

try {
  $db=new PDO("sqlite:tmp/lac2011.db"); // XXX -> config.php
} catch (PDOException $exception) {
  die ('Database Failure: '.$exception->getMessage());
}

$res=$db->query($q);
if (!$res)
  printerror('database query failed.');
$result=$res->fetchAll();
if (count($result) ==0)
  printerror('No matching entries found');
$v=$result[0];

global $hidepapers;
if ($hidepapers) $v['url_paper'] = '';

$url=$v['url_stream'];
$w=720;$h=576;
$ua=$_SERVER['HTTP_USER_AGENT'];
$jar='static/cortado_url.jar';
#print_r($url);
#print_r($ua);


if (stripos($ua, 'firefox')!== false 
/*|| stripos($ua, 'chrome')!== false*/
/*|| stripos($ua, 'opera')!== false*/
) {  /* Browser HTML-5 */
  $out='
  <video width="'.$w.'" height="'.$h.'" autoplay loop controls>
    <source src="'.$url.'"  type="video/ogg" />
  </video>
';
}
else { /* JAVA - Cortado player */
  $useobject=1;

  $params='
    <param name="archive" value="'.$jar.'" />
    <param name="url" value="'.$url.'"/>
    <param name="keepaspect" value="true"/>
    <param name="local" value="false"/>
    <param name="statusHeight" value="18"/>
    <param name="live" value="false"/>
    <param name="seekable" value="true"/>
    <param name="bufferSize" value="200"/>
    <param name="duration" value="0"/>
';

  if ($useobject==0) {
    $out='
  <applet code="com.fluendo.player.Cortado.class" archive="'.$jar.'" width="'.$w.'" height="'.$h.'">
'.$params.'
  </applet>
';
  } else {
    $out='
  <object type="application/x-java-applet" data='.$jar.' width="'.$w.'" height="'.$h.'" standby="Loading Video Player..">
    <param name="classid" value="java:com.fluendo.player.Cortado.class" />
'.$params.'
    <p>
      <strong>
        This browser does not have a Java Plug-in.<br/>
        <a href="http://www.java.com/getjava" title="Download Java Plug-in">Get the latest Java Plug-in here.</a>
      </strong>
    </p>
  </object>
';
  }
}

echo '<div class="header">Linux Audio Conference 2011</div>';
echo '<div class="title">';
echo '<b>'.xhtmlify($v['title']).'</b><br/>';
echo '<em>'; $i=0;
$a_users = fetch_selectlist($db, 'user', 'ORDER BY name');
foreach (fetch_authorids($db, $id) as $user_id) {                    
  if ($i++>0) echo ', ';
  echo xhtmlify($a_users[$user_id]);
}
echo '</em>';
echo '</div>';
echo '<div class="player">'.$out.'</div>';
$tu=rawurldecode($url);
$tt=rawurldecode('LAC 2011 - '.$v['title']);
echo '
  <div id="sociallinkbar">
    <a id="lnktwtr" rel="_blank" href="http://twitter.com/share?url='.$tu.'" title="Tweet This!"></a>
    <a id="lnkturl" rel="_blank" href="http://tinyurl.com/create.php?url='.$tu.'" title="Create TinyURL"></a>
    <a id="lnkbtly" rel="_blank" href="http://bit.ly/?url='.$tu.'" title="Create bit.ly shortcut"></a>
    <a id="lnkfbok" rel="_blank" href="http://www.facebook.com/sharer.php?u='.$tu.'&amp;t='.$tt.'" title="Send to Facebook"></a>
    <a id="lnkgogl" rel="_blank" href="https://www.google.com/bookmarks/mark?op=add&amp;bkmk='.$tu.'&amp;title='.$tt.'&amp;annotation=&amp;nui=1&amp;service=bookmarks" title="Send to Google"></a>
    <a id="lnksupn" rel="_blank" href="http://www.stumbleupon.com/submit?url='.$tu.'&amp;title='.$tt.'" title="Submit to Stumble Upon"></a>
    <a id="lnkdlus" rel="_blank" href="http://delicious.com/save?v=6&amp;jump=yes&amp;url='.$tu.'&amp;title='.$tt.'" title="Bookmark this on Del.icio.us"></a>
  </div>
';

echo '<div class="links"><ul>';
echo '<li>Video: <a href="'.$url.'">'.basename($url).'</a></li>';
if (!empty($v['url_slides']))
  echo '<li>Slides: <a href="'.$v['url_slides'].'" rel="_blank">'.$v['url_slides'].'</a></li>';
if (!empty($v['url_paper']))
  echo '<li>Paper: <a href="'.$v['url_paper'].'" rel="_blank">'.$v['url_paper'].'</a></li>';
if (!empty($v['url_misc']))
  echo '<li>Site: <a href="'.$v['url_misc'].'" rel="_blank">'.$v['url_misc'].'</a></li>';
echo '</ul></div>';

if (!empty($v['abstract'])) {
  echo '<div class="abstract">';
  echo '<p>'.xhtmlify($v['abstract']).'</p>';
  echo '</div>';
}

?>
<div class="license">

<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/88x31.png" /></a><br />The video is licensed in terms of the <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons Attribution-ShareAlike 3.0 Unported License</a>. Attribute to <a xmlns:cc="http://creativecommons.org/ns#" href="http://lac.linuxaudio.org/2011/" property="cc:attributionName" rel="cc:attributionURL">linuxaudio.org</a>. All copyright(s) remain with the author/speaker/presenter.
</div>
<div class="footer">Back to <a href="index.php?page=program">conference site</a>.</div>
</body>
</html>
