<?php

  date_default_timezone_set('UTC');
  require_once('config.php');

  function authenticate($group='') {

    #HTTP DIGEST AUTH
    $user=$_SERVER['PHP_AUTH_DIGEST'];
    if (!empty($user)) {
      # TODO: check $group
      return true;
    }
    return false;
  }

  function xhtmlify($s) {
    return htmlentities($s,ENT_COMPAT,'UTF-8');
    #return htmlentities(mb_convert_encoding($s,'utf-8,'utf-8'),ENT_COMPAT,'UTF-8');
  }

  # unused ? fn
  function plaindate($e, $start=true) {
    $time= dbadmin_unixtime($e, $start);
    return date("d.M H:i", $time);
  }

  # used in TEX
  function plaintime($e, $start=true) {
    $time= dbadmin_unixtime($e, $start);
    date_default_timezone_set('USA/San Francisco');
    $rv=date("H:i", $time);
    date_default_timezone_set('UTC');
    return $rv;
  }

  function iso8601($e, $start=true) {
    $time= dbadmin_unixtime($e, $start);
    return date("Ymd\THis\Z", $time);
    #return date(DateTime::ISO8601, $time);
  }

  function limit_text($s,$l=24) {
    if (strlen($s)<=$l) return ($s);
    return (substr($s,0,$l).'..');
  }

  function bytes_to_text($bytesize) { 
    $sizearray = array('bytes', 'KiB', 'MiB', 'GiB'); 
    $d = 0; 
    while($bytesize / 1024 > 1 && $d < 3) 
      { $d++; $bytesize /= 1024.0; } 
    $unit = $sizearray[$d]; 
    if($d == 0) return number_format($bytesize) . " " . $unit; 
    else return number_format($bytesize, 2, '.', '') . " " . $unit; 
  } 

  function dirlisttable($listdir) { 
    $dir = opendir($listdir); 
    $dirarray = array(); 
    $filearray = array(); 
    $dircount = 0; $filecount = 0; 
    while ($file_name = readdir($dir)) {
      if(($file_name == ".") || ($file_name == "..") || ($file_name == "index.php")) continue;
      if (is_dir($listdir.'/'.$file_name)) { $dirarray[$dircount] = $file_name; $dircount++; } 
      else { $filearray[$filecount] = $file_name; $filecount++; }
    }
    sort($dirarray); reset($dirarray); sort($filearray); reset($filearray);
    closedir($dir); clearstatcache(); 

    echo '<div><h2>Dir Listing</h2>';
    echo '<table cellspacing="2" cellpadding="2" border="0">'; 
    echo '<tr><th align="center"><b>Filename</b></th><th align="center"><b>Size</b></th></tr>'; 
    #echo '<tr><th align="center"><b>Filename</b></th><th align="center"><b>Size</b></th><th align="center"><b>Created</b></th></tr>'; 

    for($a = 0; $a < $dircount; $a++) {
      echo '<tr><td><a href="download/'.$dirarray[$a].'">'.$dirarray[$a].'</a></td><td>DIRECTORY</td><td></td></tr>'; 
    } 

    #echo '</table><table cellspacing="4" cellpadding"6">'; 
    for($b = 0; $b < $filecount; $b++) { 
       $currentfile = $listdir.'/'.$filearray[$b]; 
       $size = bytes_to_text((double) filesize($currentfile)); //Filesize UI 
       $time = strftime ("%b %d %Y %H:%M:%S", filectime($currentfile)); 
       echo '<tr><td><a href="'.$listdir.'/'.$filearray[$b].'">'.$filearray[$b].'</a></td><td align="right">'.$size.'</td></tr>'; 
       #echo '<tr><td><a href="'.$listdir.'/'.$filearray[$b].'">'.$filearray[$b].'</a></td><td align="right">'.$size.'</td><td align="right">'.$time.'</td></tr>'; 
    }
    echo '</table>';
    echo '</div>';
	}


	# libadmin

  function admin_fieldset() {
?>
<fieldset class="fm">
    <legend>Registration Admin:</legend>
    <input class="button" type="button" title="List all registrations" value="List Participants" onclick="admingo('admin','list','');"/>
		&nbsp;
    <input class="button" type="button" title="Show non empty remarks" value="List Remarks" onclick="admingo('admin','remarks','');"/>
		&nbsp;
    <input class="button" type="button" title="Generate list of email addresses" value="Dump Email Contacts" onclick="admingo('admin','email','');"/>
    <br/>
    <input class="button" type="button" title="Count Ordered Proceedings" value="Count Ordered Proceedings" onclick="admingo('admin','proceedings','');"/>
<!-- <input class="button" type="button" title="Show Badges TeX" value="Show Badges TeX" onclick="admingo('admin','badgestex','');"/> !-->
		&nbsp;
    <input class="button" type="button" title="Generate badges PDF" value="Generate Badges PDF" onclick="admingo('admin','badgespdf','');"/>
		&nbsp;
    <input class="button" type="button" title="Export comma separated value table" value="Export registrations (CSV)" onclick="admingo('admin','csv','');"/>
    <br/>
  </fieldset>

  <fieldset class="fm">
    <legend>Program Schedule Admin Menu:</legend>
		<input class="button" type="button" title="List Program Entries" value="List Program Entries" onclick="admingo('adminschedule','','');"/>
		&nbsp;
		<input class="button" type="button" title="List Authors" value="List Authors" onclick="admingo('adminschedule','listuser','');"/>
		&nbsp;
    <input class="button" type="button" title="List Locations" value="List Locations" onclick="admingo('adminschedule','listlocation','');"/>
		<br/>
    <input class="button" type="button" title="Add Program Entry" value="Add Program Entry" onclick="admingo('adminschedule','edit','-1');"/>
		&nbsp;
		<input class="button" type="button" title="Add Author" value="Add Author" onclick="admingo('adminschedule','edituser','-1');"/>
		&nbsp;
		<input class="button" type="button" title="Add Location" value="Add Location" onclick="admingo('adminschedule','editlocation','-1');"/>
		<br/>
		<input class="button" type="button" title="Check Timetable for conflicts" value="Check Timetable for conflicts" onclick="admingo('adminschedule','conflicts','');"/>
		&nbsp;
		<input class="button" type="button" title="List orphaned entries" value="List orphaned entries" onclick="admingo('adminschedule','orphans','');"/>
		&nbsp;
		<input class="button" type="button" title="Export Program (CSV)" value="Export Program (CSV)" onclick="admingo('adminschedule','export','');"/>
		<br/>
  </fieldset>
<?php
	}

function texify_umlauts($v) {
  $v=str_replace("\xc3\x9f",'\\"{s}',$v);
  $v=str_replace("\xc3\xa0",'\\`{a}',$v);
  $v=str_replace("\xc3\xa1",'\\\'{a}',$v);
  $v=str_replace("\xc3\xa2",'\\\^{a}',$v);
  $v=str_replace("\xc3\xa4",'\\"{a}',$v);
  $v=str_replace("\xc3\xa8",'\\`{e}',$v);
  $v=str_replace("\xc3\xa9",'\\\'{e}',$v);
  $v=str_replace("\xc3\xaa",'\\^{e}',$v);
  $v=str_replace("\xc3\xb6",'\\"{o}',$v);
  $v=str_replace("\xc3\xb9",'\\`{u}',$v);
  $v=str_replace("\xc3\xba",'\\\'{u}',$v);
  $v=str_replace("\xc3\xbc",'\\"{u}',$v);
  $v=str_replace("\xc3\xbd",'\\\'{y}',$v);
  $v=str_replace("\xc3\xbf",'\\"{y}',$v);
  $v=str_replace("&",'\&',$v);
  return $v;
}
