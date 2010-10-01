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
    echo '<tr><th align="center"><b>Filename</b></th><th align="center"><b>Size</b></th><th align="center"><b>Created</b></th></tr>'; 

    for($a = 0; $a < $dircount; $a++) {
      echo '<tr><td><a href="download/'.$dirarray[$a].'">'.$dirarray[$a].'</a></td><td>DIRECTORY</td><td></td></tr>'; 
    } 

    #echo '</table><table cellspacing="4" cellpadding"6">'; 
    for($b = 0; $b < $filecount; $b++) { 
       $currentfile = $listdir.'/'.$filearray[$b]; 
       $size = bytes_to_text((double) filesize($currentfile)); //Filesize UI 
       $time = strftime ("%b %d %Y %H:%M:%S", filectime($currentfile)); 
       echo '<tr><td><a href="'.$listdir.'/'.$filearray[$b].'">'.$filearray[$b].'</a></td><td align="right">'.$size.'</td><td align="right">'.$time.'</td></tr>'; 
    }
    echo '</table>';
    echo '</div>';
  }
