<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <title>LAC2012 Program: The Linux Audio Conference Timetable </title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="static/style.css" type="text/css"/>
  <meta name="Author" content="Robin Gareus" />
  <meta name="description" content="Linux Audio Conference 2012" />
	<meta name="keywords" content="LAC2012, LAC, Linux Audio Conference 2012, Linux, Music, Audio, Developer Meeting, CCRMA, Computer Research in Music and Acoustics, Stanford, Stanford University" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <script type="text/javascript" src="static/script.js"></script>
</head>
<body id="content" style="border:0px; background:#ffffff;">
<div>
<?php
  require_once('config.php');
  require_once('lib.php');
  require_once('programdb.php');
  $filter=array('user' => '0', 'day' => '0', 'type' => '0', 'location' => '0', 'id' => '0');
  if (isset($_REQUEST['pdb_filterid'])) {
    $filter['id'] = intval(rawurldecode($_REQUEST['pdb_filterid']));
    list_filtered_program($db, $filter, 1);
  }
?>
</div>
</body>
</html>
