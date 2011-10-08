<?php
# vim: ts=2 et
  require_once('lib/lib.php');
  require_once('lib/programdb.php');
  xhtmlhead('Conference Timetable', 'printstyle.css', '<link rel="stylesheet" type="text/css" media="print" href="'.BASEURL.'static/print.css" />');
?>
<body id="content">
<div class="menu">Back to the <a href="<?=local_url('program')?>">conference site</a>.</div>
<h1 class="center"><?=SHORTTITLE?> - Conference Programme</h1>
<div class="center"><?=$config['headerlocation']?></div>
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
