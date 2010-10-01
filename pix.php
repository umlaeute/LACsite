<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <title>LAC2010 Picturei Gallery</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="style.css" type="text/css"/>
  <meta name="Author" content="Robin Gareus" />
  <meta name="description" content="Linux Audio Conference 2010" />
  <meta name="keywords" content="LAC2010, LAC, Linux Audio Conference 2010, Hogeschool voor de Kunsten, Linux, Music, Developer Meeting, Utrecht" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <script type="text/javascript" src="script.js"></script>
</head>
<body id="content" style="border:0px;">
<?php
  require_once('config.php');
  require_once('imgarray.php');
	$id=intval(rawurldecode($_REQUEST['id']));
	$k=array_keys($frankimg);
	$v=array_values($frankimg);
	$m=count($k);

	if ($id<0 || $id>=$m) {
		echo '<h1>invalid image ID.</h1>';
		exit;
	}

	if (($id+1)<$m) echo '<div style="float:right"><a href="pix.php?id='.($id+1).'">Next</a></div>';
	if ($id>0) echo '<div><a href="pix.php?id='.($id-1).'">Prev</a></div>';
  echo '<div style="clear:both;"> </div>';


	echo '<p class="pix">'.htmlentities($v[$id],ENT_COMPAT,'UTF-8').'</p>';
	echo '<p><a href="img/frank/'.$k[$id].'">';
	echo '<img src="img/frank/'.$k[$id].'" alt="'.htmlentities($v[$id],ENT_COMPAT,'UTF-8').'" width="100%"/>';
	echo '</a></p>';
?>
</body>
</html>
