<?php

  function program_header($mode,$details) {
    echo '<h1>Conference Schedule</h1>'."\n";
### Note during conference about streaming and IRC ###
    echo '<div class="center" style="margin-top:.5em; margin-bottom:.-5em;"><p>During the conference, live A/V streams are available for the main track.</p><p>Remote participants are invited to join <a href="http://webchat.freenode.net/?channels=lac2012" rel="external">#lac2012 on irc.freenode.net</a>, to be able to take part in the discussions, ask questions, and get technical assistance in case of stream problems.</p><p>Conference Material can be found on the <a href="'.local_url('files').'">Download Page</a>.</p><br/></div>';
    #echo '<iframe src="streamsrc.php" style="width:100%; height:12em; border:0px;"></iframe>';
    #echo '<div innerHTML="streamsrc.php" style="width:100%; border:0px;">';
    #require_once('streamsrc.php');
    #echo '</div>';

    echo '<br/><hr/>'."\n";

    echo '<p class="ptitle">Timetable Format: ';
    if ($mode!='list' || $details)
      echo '<a href="'.local_url('program', 'mode=list&amp;details=0').'">Plain List</a>&nbsp;|&nbsp;';
    if ($mode!='list' || !$details)
      echo '<a href="'.local_url('program', 'mode=list&amp;details=1').'">List with Abstracts</a>&nbsp;|&nbsp;';
    if ($mode!='table')
      echo '<a href="'.local_url('program', 'mode=table').'">Table</a>&nbsp;|&nbsp;';
    #echo 'vCal (<a href="vcal.php?v=1.0">v1.0-iCal</a>)&nbsp;';
    #echo '(<a href="vcal.php">v2.0</a>)&nbsp;|&nbsp;';
    echo '<a href="vcal.php">iCal</a>&nbsp;|&nbsp;';
    echo '<a href="printprogram.php">Printable Version</a>'."\n";
    echo '<br/>All times are <a href="http://en.wikipedia.org/wiki/Pacific_Daylight_Time" rel="external">PDT</a> = UTC-7'."\n";
    echo "</p>\n";
  }
### Note before conference about streaming and IRC ###

  $mode='list';
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
      $now=time(); $day=1;
      $days=count(array_keys(fetch_selectlist(0,'days')));
      for($cday=1; $cday <= $days; $cday++) {
        if ($now < conference_dayend($cday)) {$day=$cday; break;}
      }
      if (isset($_REQUEST['day'])) $day=intval($_REQUEST['day']);
      if ($day<1 || $day>$days) {
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

# vim: ts=2 et
