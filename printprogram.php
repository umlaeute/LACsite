<?php
  require_once('lib/lib.php');
  require_once('lib/programdb.php');
	xhtmlhead('Conference Timetable', 'printstyle.css');
?>
<body id="content">
<h1 class="center">LAC 2012 Conference Programme</h1>
<div class="center">April 12-15; CCRMA, Stanford University</div>
<div class="center">All times are PDT = UTC-7</div>
<?php
  require_once('lib/lib.php');
  require_once('lib/programdb.php');

  table_program($db,1,true);
  table_program($db,2,true);
  table_program($db,3,true);
  table_program($db,4,true);
  hardcoded_concert_and_installation_info($db, true);
  hardcoded_disclaimer();
?>
</body>
</html>
