<?php

  function program_header($mode,$details) {
    echo '<h1>Conference Programme - Clár na Comhdhála</h1>'."\n";
    echo '<div class="center" style="margin-top:.5em; margin-bottom:.-5em;">Live A/V streams are available at <a href="http://streamer.stackingdwarves.net/" rel="external">http://streamer.stackingdwarves.net/</a><br/>Backup server: <a href="http://radio.linuxaudio.org/" rel="external">http://radio.linuxaudio.org/</a><p>Remote participants are invited to join <a href="http://webchat.freenode.net/?channels=lac2011" rel="external">#lac2011 on irc.freenode.net</a>, to be able to take part in the discussions, ask questions, and get technical assistance in case of stream problems.</p><p>Conference Material can be found on the <a href="?page=download">Download Page</a>.</p><br/></div>';
    echo '<p class="ptitle">Timetable Format: ';
    if ($mode!='list' || $details)
      echo '<a href="?page=program&amp;mode=list&amp;details=0">Plain List</a>&nbsp;|&nbsp;';
    if ($mode!='list' || !$details)
      echo '<a href="?page=program&amp;mode=list&amp;details=1">List with Abstracts</a>&nbsp;|&nbsp;';
    if ($mode!='table')
      echo '<a href="?page=program&amp;mode=table">Table</a>&nbsp;|&nbsp;';
    #echo 'vCal (<a href="vcal.php?v=1.0">v1.0-iCal</a>)&nbsp;';
    #echo '(<a href="vcal.php">v2.0</a>)&nbsp;|&nbsp;';
    echo '<a href="vcal.php">iCal</a>&nbsp;|&nbsp;';
    echo '<a href="printprogram.php">Printable Version</a>';
    echo "</p>\n";
  }

  $mode='table';
  if (isset($_REQUEST['mode'])&&!empty($_REQUEST['mode'])) $mode=$_REQUEST['mode'];
  #$details=isset($_REQUEST['details'])?true:false;
  $details=isset($_REQUEST['details'])?($_REQUEST['details']?true:false):true;

  switch ($mode) {
    case 'vcal':
      program_header($mode,$details);
      echo '<p>Download Link: <a href="vcal.php">vCal/iCal</a><br/><br/></p><hr>';
      echo '<pre>';
      vcal_program($db,'2.0',false);
      echo '</pre><hr/>';
      break;
    case 'table':
      $now=time();
      if      ($now < 1307311200) $day=1;
      else if ($now < 1309903200) $day=2;
      else if ($now > 1312581600) $day=1;
      else $day=3;

      if (isset($_REQUEST['day'])) $day=intval($_REQUEST['day']);
      if ($day<1 || $day>4) {
        program_header('',$details);
        hardcoded_concert_and_installation_info($db);
      } else {
        program_header($mode,$details);
        table_program($db,$day);
      }
      break;
    default:
    case 'list':
      program_header($mode,$details);
      if ($f=print_filter($db))
        list_filtered_program($db, $f, $details);
      else
        list_program($db, $details);
      break;
  }
  hardcoded_disclaimer();

?>
