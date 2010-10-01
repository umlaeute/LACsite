<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <title>LAC2011 Program: The Linux Audio Conference Timetable </title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="static/printstyle.css" type="text/css"/>
  <meta name="Author" content="Robin Gareus" />
  <meta name="description" content="Linux Audio Conference 2011" />
  <meta name="keywords" content="LAC2011, LAC, Linux Audio Conference 2011,Linux, Music, Developer Meeting, Music Department, National University of Ireland, Maynooth" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
</head>
<body id="content">
<h1 class="center">LAC 2011 Timetable</h1>
<div class="center">May 6-8; Music Department, National University of Ireland, Maynooth</div>
<?php
  require_once('config.php');
  require_once('lib.php');
  require_once('programdb.php');

  table_program($db,1,true);
  table_program($db,2,true);
  table_program($db,3,true);
  hardcoded_concert_and_installation_info($db);
  hardcoded_disclaimer();
?>
</body>
</html>
