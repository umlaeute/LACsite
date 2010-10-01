<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <title>LAC2010 Program: The Linux Audio Conference Timetable </title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="printstyle.css" type="text/css"/>
  <meta name="Author" content="Robin Gareus" />
  <meta name="description" content="Linux Audio Conference 2010" />
  <meta name="keywords" content="LAC2010, LAC, Linux Audio Conference 2010, Hogeschool voor de Kunsten, Linux, Music, Developer Meeting, Utrecht" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
</head>
<body id="content">
<h1 class="center">LAC 2010 Timetable</h1>
<div class="center">May 1-4; Hogeschool voor de Kunsten Utrecht, Lange Viestraat 2, Utrecht, NL</div>
<?php
  require_once('config.php');
  require_once('lib.php');
  require_once('programdb.php');

  table_program($db,1,true);
  table_program($db,2,true);
  table_program($db,3,true);
  table_program($db,4,true);
  hardcoded_concert_and_installation_info($db);
  hardcoded_disclaimer();
?>
</body>
</html>
