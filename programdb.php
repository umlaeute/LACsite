<?php
  try {
    $db=new PDO("sqlite:tmp/lac2011.db"); // XXX -> config.php
  } catch (PDOException $exception) {
    die ('Database Failure: '.$exception->getMessage());
  }
  $db->sqliteCreateFunction("typesort", "typesort", 1);

  // returns -1 if lock was aquired; return time until lock expires
  function lock($db, $id, $table='activity') {
    if ($id<0) return -1; # ignore 'new' entries.
    $q='UPDATE '.$table.' set editlock=datetime(\'now\',\'+5 minutes\') WHERE editlock < datetime(\'now\') AND id='.$id.';';
    if ($db->exec($q) == 1) return -1;

    $q='SELECT editlock from '.$table.' WHERE id='.$id.';';
    $res=$db->query($q);
    if (!$res) return 0; # XXX error
    $r=$res->fetch(PDO::FETCH_ASSOC);
    return $r['editlock'];
  }

  function unlock($db,$id, $table='activity') {
    if ($id<0) return; # ignore 'new' entries.
    $q='UPDATE '.$table.' set editlock=0 WHERE id='.$id.';';
    $db->exec($q);
  }

  function typesort($t) {
    switch ($t) {
      case 'p': return 0;
      case 'w': return 1;
      case 'i': return 2;
      case 'c': return 3;
      default:  return 4;
    }
  }

  function color_type($t) {
    switch ($t) {
      case 'p': $col='#FF0000'; break;
      case 'w': $col='#CC8833'; break;
      case 'i': $col='#00AA88'; break;
      case 'c': $col='#00FF00'; break;
      default:  $col='#0000FF'; break;
    }
    return '<span style="color:'.$col.';">'.$t.'</span>';
  }

	function track_legend() {
		# XXX - hardcoded session/track XXX
		$rv='<div style="width:100%; margin:.5em;">';
		$rv.='<table cellspacing="0" class="trl">';
		$rv.='<tr>';
		$rv.='<td class="trX" colspan="4">Color Legend</td>';
		$rv.='</tr><tr>';
		$rv.='<td class="tr1">'.track_name('tr1').'</td>';
		$rv.='<td class="tr3">'.track_name('tr3').'</td>';
		$rv.='<td class="tr5">'.track_name('tr5').'</td>';
		$rv.='<td class="tr7">'.track_name('tr7').'</td>';
		$rv.='</tr><tr>';
		$rv.='<td class="tr2">'.track_name('tr2').'</td>';
		$rv.='<td class="tr4">'.track_name('tr4').'</td>';
		$rv.='<td class="tr6">'.track_name('tr6').'</td>';
		$rv.='<td class="tr0">Ilghnéitheach</td>';
		$rv.='</tr>';
		$rv.='</table></div>';
		return $rv;
	}

	function track_name($tr) {
		# XXX - hardcoded session/track XXX
		switch ($tr) {
			case 'tr1': return 'Music Programming Languages';
			case 'tr2': return 'Audio Infrastructure and Broadcast';
			case 'tr3': return 'Interfaces for Music Instruments';
			case 'tr4': return 'Sound Synthesis';
			case 'tr5': return 'Systems and Language';
			case 'tr6': return 'Audio Programming';
			case 'tr7': return 'Environments and Composition';
			default: return '';
		}
	}
	function track_color($d) {
		# XXX - hardcoded session/track XXX
		if ($d['day'] == 1 && $d['starttime'] < '13:00') return 'tr1';
		if ($d['type'] != 'p') return 'tr0';

		if ($d['day'] == 1 && $d['starttime'] < '16:00') return 'tr4';
		if ($d['day'] == 1) return 'tr5';

		if ($d['day'] == 2 && $d['starttime'] < '13:00') return 'tr2';
		if ($d['day'] == 2 && $d['starttime'] < '16:00') return 'tr3';
		if ($d['day'] == 2) return 'tr5';

		if ($d['day'] == 3 && $d['starttime'] < '13:00') return 'tr6';
		if ($d['day'] == 3 && $d['starttime'] > '13:00') return 'tr7';
		return 'tr0';
	}

  function translate_type($t) {
    switch ($t) {
      case 'p': return 'Paper Presentation';
      case 'w': return 'Workshop';
      case 'i': return 'Installation';
      case 'c': return 'Concert';
      default: return 'other';
    }
  }

  function say_db_message($msg='') {
    if ($msg)
      echo '<div class="dbmsg">'.$msg.'</div>'."\n";
  }

  function say_db_error($msg='unknown') {
    global $db;
    echo 'DATABASE ERROR: "'.$msg.'" '.print_r($db->errorInfo(),true)."\n";
  }

  function _slv($k, $c) {
    if ($k == $c) return ' selected="selected"';
    return '';
  }

  function gen_options ($d,$k) {
    foreach ($d as $v => $t) {
      echo '    <option value="'.xhtmlify($v).'"'._slv($k,$v).'>'.xhtmlify($t).'</option>'."\n";
    }
  }

  function fetch_activity_by_location($db, $location_id) {
    $rv = array();
    $q='SELECT DISTINCT title, id from activity
       WHERE location_id ='.$location_id.';'; 
    $res=$db->query($q);
    if (!$res) { say_db_error('authorids'); return $rv;}
    $result=$res->fetchAll();
    foreach ($result as $r) {
      $rv[]=$r;
    }
    return $rv;
  }


  function fetch_activity_by_author($db, $user_id) {
    $rv = array();
    #$q='SELECT activity_id from usermap where user_id='.$user_id.';'; 
    $q='SELECT DISTINCT activity.title AS title, activity.id AS id, activity.day AS day, activity.starttime AS starttime, activity.duration AS duration, activity.type AS type, usermap.activity_id from activity,usermap 
       WHERE activity.id = usermap.activity_id AND usermap.user_id='.$user_id.';'; 
    $res=$db->query($q);
    if (!$res) { say_db_error('authorids'); return $rv;}
    $result=$res->fetchAll();
    foreach ($result as $r) {
      $rv[]=$r;
    }
    return $rv;
  }

  function fetch_authorids($db, $activity_id) {
    $rv = array();
    $q='SELECT user_id from usermap where activity_id='.$activity_id.';'; 
    $res=$db->query($q);
    if (!$res) { say_db_error('authorids'); return $rv;}
    $result=$res->fetchAll();
    foreach ($result as $r) {
      $rv[]=intval($r['user_id']);
    }
    return $rv;
  }

  function fetch_selectlist($db, $table='user', $order='ORDER BY id') {
    if ($table=='days')
      return array('1' => '1 - Friday', '2' => '2 - Saturday', '3' => '3 - Sunday');
    if ($table=='types')
      #return array('p' => 'Paper Presentation', 'w' => 'Workshop', 'c' => 'Concert', 'i' => 'Installation', 'o' => 'Other');
      return array('p' => 'Paper Presentation', 'w' => 'Workshop', 'c' => 'Concert', 'o' => 'Other');
    if ($table=='durations')
      return array('' => '-unset-', '15' => '15 mins', '30' => '30 mins', '45' => '45 mins', '60' => '1 hour', '90' => '90 mins', '120' => '2 hours', '150' => '2 1/2 hours', '180' => '3 hours');
    if ($table=='status')
      return array('1' => 'confirmed', '0' => 'cancelled');

    $rv = array('0' => '-unset-');
    $q='SELECT id, name from '.$table.' '.$order.';'; 
    $res=$db->query($q);
    if (!$res) { say_db_error(); return $rv;}
    $result=$res->fetchAll();
    foreach ($result as $r) {
      $rv[$r['id']] = $r['name'];
    }
    return $rv;
  }

  function dbadmin_listlocations($db) {
    $q='SELECT id, name from location ORDER BY id;'; 
    $res=$db->query($q);
    if (!$res) { say_db_error(); return $rv;}
    $result=$res->fetchAll();
    echo '<table class="adminlist" cellspacing="0">'."\n";
    echo '<tr><th></th><th>Location</th><th>#talks</td><th>&nbsp;</th>';
    $alt=0;
    foreach ($result as $r) {
      $aids=fetch_activity_by_location($db,$r['id']);
      echo '<tr'.(($alt++%2==1)?' class="alt"':'').'>';
      echo '<td>('.$r['id'].')&nbsp;</td><td>'.xhtmlify($r['name']).'</td>';
      echo '<td class="center'.((count($aids)==0)?' red':'').'">'.count($aids).'</td><td>';
      echo '<a class="active" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'editlocation\';document.myform.submit();">Edit</a>';
      echo '&nbsp;|&nbsp;';
      echo '<a class="active" onclick="if (confirm(\'Really delete Location no. '.$r['id'].'?\')) {document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'dellocation\';document.myform.submit();i}">Delete</a>';
      echo '</td></tr>'."\n";
    }
    echo '</table>'."\n";
  }

  function dbadmin_listusers($db) {
    $q='SELECT id, name, email, bio from user ORDER BY name;'; 
    $res=$db->query($q);
    if (!$res) { say_db_error(); return $rv;}
    $result=$res->fetchAll();
    echo '<input name="pdb_filterauthor" type="hidden" value="0" id="pdb_filterauthor"/>';
    echo '<table class="adminlist" cellspacing="0">'."\n";
    echo '<tr><th></th><th>Username</th><th>#talks</td><th>Email</th><th>Short Bio</th><th>&nbsp;</th>';
    $alt=0;
    $emaillist='';
    foreach ($result as $r) {
      $aids=fetch_activity_by_author($db,$r['id']);
      echo '<tr'.(($alt++%2==1)?' class="alt"':'').'>';
      echo '<td>('.$r['id'].')&nbsp;</td><td>'.xhtmlify($r['name']).'</td>';
      if (count($aids)==0)
        echo '<td class="center red">0</td>';
      else 
        echo '<td class="center"><a class="active" onclick="document.getElementById(\'pdb_filterauthor\').value=\''.$r['id'].'\';document.getElementById(\'param\').value=\'-1\';document.getElementById(\'mode\').value=\'\';document.myform.submit();">'.count($aids).'</a></td>';
      echo '<td>'.xhtmlify($r['email']).'</td>';
      if (!empty($r['email']))
        $emaillist.=$r['email'].', ';
      echo '<td>'.limit_text($r['bio'],300).'</td><td>';
      echo '<a class="active" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'edituser\';document.myform.submit();">Edit</a>';
      echo '&nbsp;|&nbsp;';
      echo '<a class="active" onclick="if (confirm(\'Really delete User no. '.$r['id'].'?\')) {document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'deluser\';document.myform.submit();i}">Delete</a>';
      echo '</td></tr>'."\n";
    }
    echo '</table>'."\n";
    echo '<hr/>'."\n";
    echo '<pre style="font-size:9px; background:#ccc; line-height:1.3em;margin-top:2em;">';
    echo wordwrap($emaillist,100);
    echo '</pre><br/>'; 
  }

  function dbadmin_listall($db, $order='time') {
    $a_users = fetch_selectlist($db, 'user', 'ORDER BY name');
    $a_locations = fetch_selectlist($db, 'location');
    $a_days = fetch_selectlist(0, 'days');
    $a_types = fetch_selectlist(0, 'types');

    $filter=array('user' => '0', 'day' => '0', 'type' => '0');

    if ($_REQUEST['param'] == -1) { // filter enable
      if (isset($_REQUEST['pdb_filterday'])) $filter['day'] = intval(rawurldecode($_REQUEST['pdb_filterday']));
      if (isset($_REQUEST['pdb_filtertype'])) $filter['type'] = substr(rawurldecode($_REQUEST['pdb_filtertype']),0,1);
      if (isset($_REQUEST['pdb_filterauthor'])) $filter['user'] = intval(rawurldecode($_REQUEST['pdb_filterauthor']));
    }

    echo '<fieldset>';
    echo '<legend>Filter:</legend>';
    echo '<label for="pdb_filterday">Day:</label>';
    echo '<select id="pdb_filterday" name="pdb_filterday" size="1">';
    gen_options(array_merge(array('0' => '-all-'), $a_days) , $filter['day']);
    echo '</select>&nbsp;'."\n";

    echo '<label for="pdb_filtertype">Type:</label>';
    echo '<select id="pdb_filtertype" name="pdb_filtertype" size="1">';
    gen_options(array_merge(array('0' => '-all-'), $a_types), $filter['type']);
    echo '</select>&nbsp;'."\n";

    echo '<label for="pdb_filterauthor">Author:</label>';
    echo '<select id="pdb_filterauthor" name="pdb_filterauthor" size="1">';
    gen_options($a_users , $filter['user']);
    echo '</select>&nbsp;'."\n";
    echo '<input class="smbutton" type="button" title="Filter" value="Filter" onclick="document.getElementById(\'param\').value=-1;document.getElementById(\'mode\').value=\'\';document.myform.submit();"/>&nbsp;';
    echo '</fieldset>';

    if ($filter['user'] != '0') 
      $q = 'SELECT DISTINCT activity.*
	    FROM activity,user,usermap
	    WHERE activity.id=usermap.activity_id AND user.id=usermap.user_id
	      AND user.id='.$filter['user'];
    else 
      $q='SELECT activity.* FROM activity WHERE 1=1';

    if ($filter['type'] != '0') $q.=' AND type='.$db->quote($filter['type']);
    if ($filter['day'] > 0) $q.=' AND day='.$db->quote($filter['day']);

    if ($order=='type')
      $q.=' ORDER BY day, typesort(type), strftime(\'%H:%M\',starttime), location_id;';
    else
      $q.=' ORDER BY day, strftime(\'%H:%M\',starttime), typesort(type), location_id;';

    $res=$db->query($q);
    if (!$res) { say_db_error(); return;}
    $result=$res->fetchAll();

    if (count($result) == 0) {
      echo '<div class="center red">No matching entries found.</div>';
      return;
    }
    echo '<div class="right">'.count($result).' matching entrie(s) found.</div>';

    echo '<table class="adminlist" cellspacing="0">'."\n";
    echo '<tr><th>';
    if ($order=='type')
      echo '<span class="underline">Type</span>-Day-<a class="active" onclick="document.getElementById(\'sort\').value=\'time\';document.getElementById(\'mode\').value=\'\';document.myform.submit();">Tm</a>';
    else
      echo '<a class="active" onclick="document.getElementById(\'sort\').value=\'type\';document.getElementById(\'mode\').value=\'\';document.myform.submit();">Type</a>-Day-<span class="underline">Tm</span>';
    echo '</th><th>Title - <em>Author</em></th><th style="width:9em;">Location</th><th>&nbsp;</th></tr>'."\n";

    $alt=0;
    foreach ($result as $r) {
      echo '<tr'.(($alt++%2==1)?' class="alt"':'').'>';
      echo '<td><tt>'.color_type($r['type']).'-'.$r['day'].'-'.$r['starttime'].'</tt></td>';
      echo '<td'.(($r['status']==0)?' class="cancelled"':'').'><b>'.limit_text($r['title']).'</b>&nbsp;';

      echo '<em>'; $i=0;
      foreach (fetch_authorids($db, $r['id']) as $user_id) {
        if ($i++>0) echo ', ';
        #echo $user_id.': ';
	#echo xhtmlify($a_users[$user_id]);
        echo '<a class="active" onclick="document.getElementById(\'param\').value='.$user_id.';document.getElementById(\'mode\').value=\'edituser\';document.myform.submit();">'.xhtmlify($a_users[$user_id]).'</a>';
      }
      echo '</em></td>';

      if (!empty($r['location_id']))
        echo '<td>'.xhtmlify($a_locations[$r['location_id']]).'</td>';
      else
        echo '<td>??</td>';

      echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'edit\';document.myform.submit();">Edit</a>';
    echo '&nbsp;|&nbsp;';
    echo '<a class="active" onclick="if (confirm(\'Really delete Entry no. '.$r['id'].'?\')) {document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'delentry\';document.myform.submit();i}">Delete</a>';
      echo '</td></tr>'."\n";
    }
    echo '</table>'."\n";
  }

  function dbadmin_locationform($db, $id) {
    if ($id > 0) {
      $q='SELECT * FROM location WHERE id ='.$id.';';
      $res=$db->query($q);
      if (!$res) { say_db_error(); return;}
      $r=$res->fetch(PDO::FETCH_ASSOC);
      echo 'ID: '.$id.'<br/>';
    } else {
      $r=array('name' =>'', 'id' => -1);
      echo 'ID: new<br/>';
    }
    echo '<label for="pdb_name">Name:</label><br/>';
    echo '<input id="pdb_name" name="pdb_name" length="80" value="'.$r['name'].'" /><br/>';
    echo '<input class="button" type="button" title="Save" value="Save" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'savelocation\';document.myform.submit();"/>'."\n";
    echo '<input class="button" type="button" title="Cancel" value="Cancel" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'unlocklocation\';document.myform.submit();"/>'."<br/>&nbsp;\n";
  }


  function dbadmin_authorform($db, $id) {
    if ($id > 0) {
      $q='SELECT * FROM user WHERE id ='.$id.';';
      $res=$db->query($q);
      if (!$res) { say_db_error(); return;}
      $r=$res->fetch(PDO::FETCH_ASSOC);
      echo 'ID: '.$id.'<br/>';
    } else {
      $r=array('name' =>'', 'bio' => '', 'email' => '', 'id' => -1);
      echo 'ID: new<br/>';
    }
    echo '<label for="pdb_name">Name:</label><br/>';
    echo '<input id="pdb_name" name="pdb_name" length="80" value="'.$r['name'].'" /><br/>';
    echo '<label for="pdb_email">Email:</label><br/>';
    echo '<input id="pdb_email" name="pdb_email" length="80" value="'.$r['email'].'" /><br/>';
    echo '<label for="pdb_bio">Bio:</label><br/>';
    echo '<textarea id="pdb_bio" name="pdb_bio" rows="8" cols="70">'.$r['bio'].'</textarea><br/><br/>';

    echo '<input class="button" type="button" title="Save" value="Save" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'saveuser\';document.myform.submit();"/>'."\n";
    echo '<input class="button" type="button" title="Cancel" value="Cancel" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'unlockuser\';document.myform.submit();"/>'."<br/>&nbsp;\n";
  }

  function dbadmin_editform($db, $id) {
    $a_times = array(
                    '' => '-unset-'
                    , '9:00' => '9:00' , '9:30' => '9:30'
                    , '10:00' => '10:00' , '10:15' => '10:15' , '10:30' => '10:30' , '10:45' => '10:45'
                    , '11:00' => '11:00' , '11:15' => '11:15' , '11:30' => '11:30' , '11:45' => '11:45'
                    , '12:00' => '12:00' , '12:15' => '12:15' , '12:30' => '12:30' , '12:45' => '12:45'
                    , '13:00' => '13:00' , '13:15' => '13:15' , '13:30' => '13:30' , '13:45' => '13:45'
                    , '14:00' => '14:00' , '14:15' => '14:15' , '14:30' => '14:30' , '14:45' => '14:45'
                    , '15:00' => '15:00' , '15:15' => '15:15' , '15:30' => '15:30' , '15:45' => '15:45'
                    , '16:00' => '16:00' , '16:15' => '16:15' , '16:30' => '16:30' , '16:45' => '16:45'
                    , '17:00' => '17:00' , '17:15' => '17:15' , '17:30' => '17:30' , '17:45' => '17:45'
                    , '18:00' => '18:00' , '18:30' => '18:30'
                    , '19:00' => '19:00' , '19:30' => '19:30'
                    , '20:00' => '20:00' , '20:30' => '20:30'
                    , '21:00' => '21:00' , '21:30' => '21:30'
                    , '22:00' => '22:00' , '22:30' => '22:30'
                    , '23:00' => '23:00' , '23:30' => '23:30'
                    , '24:00' => '0:00' 
               );
    $a_durations = fetch_selectlist(0, 'durations'); 
    $a_days = fetch_selectlist(0, 'days');
    $a_types = fetch_selectlist(0, 'types');
    $a_status = fetch_selectlist(0, 'status');
    $a_locations = fetch_selectlist($db, 'location');
    $a_users = fetch_selectlist($db, 'user', 'ORDER BY name');

    if ($id > 0) {
      $q='SELECT * FROM activity WHERE activity.id ='.$id.';';

      $res=$db->query($q);
      if (!$res) { say_db_error(); return;}
      $r=$res->fetch(PDO::FETCH_ASSOC);
      echo 'ID: '.$id.'&nbsp;&nbsp;';
    } else {
      $r=array('id' => -1, 'title' => '', 'day' => 1, 'type' => 'p' , 'starttime' => '' , 'location_id' => '' , 'duration' => '' , 'abstract' => '' , 'notes' => '', 'url_paper' => '', 'url_misc' => '', 'url_audio' => '', 'url_stream' => '', 'url_slides' => '', 'url_image' => '', 'status' => 1);
      echo 'ID: new&nbsp;&nbsp;';
    }
    echo '<label for="pdb_type">Type:</label>';
    echo '<select id="pdb_type" name="pdb_type" size="1">';
    gen_options($a_types, $r['type']);
    echo '</select>&nbsp;';

    echo '<label for="pdb_status">Status:</label>';
    echo '<select id="pdb_status" name="pdb_status" size="1">';
    gen_options($a_status, $r['status']);
    echo '</select><br/>';

    echo '<label for="pdb_title">Title:</label><br/>';
    echo '<input id="pdb_title" name="pdb_title" length="80" value="'.$r['title'].'" /><br/>';
    echo '<label for="pdb_day">Day:</label>';
    echo '<select id="pdb_day" name="pdb_day" size="1">';
    gen_options($a_days , $r['day']);
    echo '</select>&nbsp;';
    echo '<label for="pdb_time">Time:</label>';
    echo '<select id="pdb_time" name="pdb_time" size="1">';
    gen_options($a_times, $r['starttime']);
    echo '</select>&nbsp;';
    echo '<label for="pdb_duration">Duration:</label>';
    if ($r['type'] != 'p') {
      echo '<input class="duration" id="pdb_duration" name="pdb_duration" length="10" value="'.$r['duration'].'" />&nbsp;';
    } else {
      echo '<select id="pdb_duration" name="pdb_duration" size="1">';
      gen_options($a_durations , $r['duration']);
      echo '</select>&nbsp;';
    }
    echo '<label for="pdb_location">Location:</label>';
    echo '<select id="pdb_location" name="pdb_location" size="1">';
    gen_options($a_locations , $r['location_id']);
    echo '</select><br/>';
    $i=1;
    if ($id>0) 
    foreach (fetch_authorids($db, $r['id']) as $user_id) {
      if ($i%2==1 && $i>1) echo '<br/>'; else if ($i>1) echo '&nbsp;';
      echo '<label for="pdb_author['.$i.']">Author '.$i.':</label>';
      echo '<select id="pdb_author['.$i.']" name="pdb_author['.$i.']" size="1">';
      gen_options($a_users , $user_id);
      echo '</select>'."\n";
      $i++;
    }
    $maxusers=6;
    while ($i+2 > $maxusers) $maxusers+=2;
    while ($i<=$maxusers) {
      if ($i%2 ==1) echo '<br/>'; else echo '&nbsp;'; 
      echo '<label for="pdb_author['.$i.']">Author '.$i.':</label>';
      echo '<select id="pdb_author['.$i.']" name="pdb_author['.$i.']" size="1">';
      gen_options($a_users , 0);
      echo '</select>'."\n";
      $i++;
    }
    echo '<br/>';

    echo '<label for="pdb_abstract">Abstract:</label><br/>';
    echo '<textarea id="pdb_abstract" name="pdb_abstract" rows="5" cols="70">'.$r['abstract'].'</textarea><br/>';

    echo '<label for="pdb_notes">Notes:</label><br/>';
    echo '<textarea id="pdb_notes" name="pdb_notes" rows="3" cols="70">'.$r['notes'].'</textarea><br/>';

    echo '<label for="pdb_url_paper">Paper URL:</label><br/>';
    echo '<input id="pdb_url_paper" name="pdb_url_paper" length="80" value="'.$r['url_paper'].'" /><br/>';
    echo '<label for="pdb_url_slides">Slides URL:</label><br/>';
    echo '<input id="pdb_url_slides" name="pdb_url_slides" length="80" value="'.$r['url_slides'].'" /><br/>';
    echo '<label for="pdb_url_audio">Audio URL:</label><br/>';
    echo '<input id="pdb_url_audio" name="pdb_url_audio" length="80" value="'.$r['url_audio'].'" /><br/>';
    echo '<label for="pdb_url_misc">Site URL:</label><br/>';
    echo '<input id="pdb_url_misc" name="pdb_url_misc" length="80" value="'.$r['url_misc'].'" /><br/>';
    echo '<label for="pdb_url_image">Image URL:</label><br/>';
    echo '<input id="pdb_url_image" name="pdb_url_image" length="80" value="'.$r['url_image'].'" /><br/>';
    echo '<label for="pdb_url_stream">Stream URL:</label><br/>';
    echo '<input id="pdb_url_stream" name="pdb_url_stream" length="80" value="'.$r['url_stream'].'" /><br/><br/>';

    echo '<input class="button" type="button" title="Save" value="Save" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'saveedit\';document.myform.submit();"/>'."\n";
    echo '<input class="button" type="button" title="Cancel" value="Cancel" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'unlockactivity\';document.myform.submit();"/>'."<br/>&nbsp;\n";
  }

  function dbadmin_dellocation($db) {
    $err=0;
    $id=intval(rawurldecode($_REQUEST['param']));
    if ($id < 0) { 
      say_db_message('Invalid Location ID given.');
      return;
    }

    $aids=fetch_activity_by_location($db,$id);
    if (count($aids) >0 ) {
      say_db_message('Location is referenced by '.count($aids).' entries and can not be deleted!');
      return;
    }

    $q='DELETE from location WHERE id='.$id.';';
    $err|=($db->exec($q) !== 1)?1:0;
    say_db_message('Deleted Location-ID='.$id.'.. '.($err==0?'OK':'Error:'.$err));
  }

  function dbadmin_deluser($db) {
    $err=0;
    $id=intval(rawurldecode($_REQUEST['param']));
    if ($id < 0) { 
      say_db_message('Invalid User ID given.');
      return;
    }

    $aids=fetch_activity_by_author($db,$id);
    if (count($aids) >0 ) {
      say_db_message('User is referenced by '.count($aids).' entries and can not be deleted!');
      return;
    }

    $q='DELETE from user WHERE id='.$id.';';
    $err|=($db->exec($q) !== 1)?1:0;
    say_db_message('Deleted User-ID='.$id.'.. '.($err==0?'OK':'Error:'.$err));
  }

  function dbadmin_delentry($db) {
    $err=0;
    $id=intval(rawurldecode($_REQUEST['param']));
    if ($id < 0) { 
      say_db_message('Invalid Enrty ID given.');
      return;
    }

    $aids=fetch_authorids($db,$id);
    if (count($aids) >0 ) {
      say_db_message('This entry references '.count($aids).' Authors. Please first unlink them.!');
      return;
    }

    $q='DELETE from activity WHERE id='.$id.';';
    $err|=($db->exec($q) !== 1)?1:0;
    say_db_message('Deleted Entry-ID='.$id.'.. '.($err==0?'OK':'Error:'.$err));
  }

  function dbadmin_savelocation($db) {
    $err=0;
    $id=intval(rawurldecode($_REQUEST['param']));
    if ($id < 0) {
      $q='INSERT into location (name) VALUES ('
	.' '.$db->quote(rawurldecode($_REQUEST['pdb_name']))
	.');';
    } else {
      unlock($db, $id, 'location');
      $q='UPDATE location set '
	.' name='.$db->quote(rawurldecode($_REQUEST['pdb_name']))
	.' WHERE id='.$id.';';
    }
    #print_r($q);
    $err|=($db->exec($q) !== 1)?1:0;
    if ($id<0)
      $id=$db->lastInsertId();
    echo '<div class="dbmsg">Saving Location-ID='.$id.'.. '.($err==0?'OK':'Error:'.$err).'</div>'."\n";
  }

  function dbadmin_saveuser($db) {
    $err=0;
    $id=intval(rawurldecode($_REQUEST['param']));
    if ($id < 0) {
      $q='INSERT into user (name, email, bio) VALUES ('
	.' '.$db->quote(rawurldecode($_REQUEST['pdb_name']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_email']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_bio']))
	.');';
    } else {
      unlock($db, $id, 'user');
      $q='UPDATE user set '
	.' name='.$db->quote(rawurldecode($_REQUEST['pdb_name']))
	.',email='.$db->quote(rawurldecode($_REQUEST['pdb_email']))
	.',bio='.$db->quote(rawurldecode($_REQUEST['pdb_bio']))
	.' WHERE id='.$id.';';
    }
    #print_r($q);
    $err|=($db->exec($q) !== 1)?1:0;
    if ($id<0)
      $id=$db->lastInsertId();
    echo '<div class="dbmsg">Saving User-ID='.$id.'.. '.($err==0?'OK':'Error:'.$err).'</div>'."\n";
  }

  function dbadmin_saveedit($db) {
    $err=0;
    $id=intval(rawurldecode($_REQUEST['param']));
    if ($id < 0) {
      $q='INSERT INTO activity (title, abstract, notes, duration, starttime, location_id, day, type, url_stream, url_paper, url_slides, url_audio, url_misc, url_image, status) VALUES ('
	.' '.$db->quote(rawurldecode($_REQUEST['pdb_title']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_abstract']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_notes']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_duration']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_time']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_location']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_day']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_type']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_url_stream']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_url_paper']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_url_slides']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_url_audio']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_url_misc']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_url_image']))
	.','.$db->quote(rawurldecode($_REQUEST['pdb_status']))
	.');';
    } else {
      unlock($db, $id, 'activity');
      $q='UPDATE activity set'
	.' title='.$db->quote(rawurldecode($_REQUEST['pdb_title']))
	.',abstract='.$db->quote(rawurldecode($_REQUEST['pdb_abstract']))
	.',notes='.$db->quote(rawurldecode($_REQUEST['pdb_notes']))
	.',duration='.$db->quote(rawurldecode($_REQUEST['pdb_duration']))
	.',starttime='.$db->quote(rawurldecode($_REQUEST['pdb_time']))
	.',location_id='.$db->quote(rawurldecode($_REQUEST['pdb_location']))
	.',day='.$db->quote(rawurldecode($_REQUEST['pdb_day']))
	.',type='.$db->quote(rawurldecode($_REQUEST['pdb_type']))
	.',url_stream='.$db->quote(rawurldecode($_REQUEST['pdb_url_stream']))
	.',url_paper='.$db->quote(rawurldecode($_REQUEST['pdb_url_paper']))
	.',url_slides='.$db->quote(rawurldecode($_REQUEST['pdb_url_slides']))
	.',url_audio='.$db->quote(rawurldecode($_REQUEST['pdb_url_audio']))
	.',url_misc='.$db->quote(rawurldecode($_REQUEST['pdb_url_misc']))
	.',url_image='.$db->quote(rawurldecode($_REQUEST['pdb_url_image']))
	.',status='.$db->quote(rawurldecode($_REQUEST['pdb_status']))
	.' WHERE id='.$id.';';
    }
    #print_r($q);
    $err|=($db->exec($q) !== 1)?1:0;

    if ($id<0)
      $id=$db->lastInsertId();

    $q='DELETE from usermap where activity_id ='.$id.';';
    $err|=($db->exec($q) >=0)?0:2;

    foreach ($_REQUEST['pdb_author'] as $author) {
      if ($author == 0) continue;
      $q='INSERT into usermap (\'activity_id\', \'user_id\') VALUES ('
          .$id.',' 
          .intval(rawurldecode($author)).');';
      $err|=($db->exec($q) !== 1)?4:0;
    }

    echo '<div class="dbmsg">Saving ID='.$id.'.. '.($err==0?'OK':'Error:'.$err).'</div>'."\n";
  }

  function query_out($db, $q, $details=true, $type=true, $location=true, $day=false) {
    $a_users = fetch_selectlist($db);
    $a_locations = fetch_selectlist($db, 'location');
    if ($day)
      $a_days = fetch_selectlist(0, 'days');

    $res=$db->query($q);
    if (!$res) return; // TODO: print error msg

    $result=$res->fetchAll();

    if (count($result) ==0 ) {
      echo '<div class="center red">No matching entries found.</div>';
    }

    foreach ($result as $r) {
      if ($day)
        echo 'Day '.$a_days[$r['day']].'&nbsp;';
			echo '<div class="righttr '.track_color($r).'">';
			echo track_name(track_color($r));
			echo '</div>';
      echo '<span class="tme">'.$r['starttime'].'</span>&nbsp;';
      if ($r['status']==0) echo '<span class="red">Cancelled: </span>';
      echo '<span'.(($r['status']==0)?' class="cancelled"':'').'><b>'.xhtmlify($r['title']).'</b></span>';
      if ($type)
        echo ' - <span>'.translate_type($r['type']).'</span>';
      #echo '<br/>';
      echo '<br style="clear:right;"/>';
      #echo '<div style="clear:right;"/></div>';
      #if (!empty($r['url_stream'])) $r['url_image']='img/authors/nando_jason.png'; # XXX
      if (!empty($r['url_image'])) {
        $thumb=$r['url_image'];
        if (strncmp($thumb,'img/authors/',12) == 0) {
          $thumb='img/authors/small/'.basename($r['url_image']);
        }
        echo '<div class="aimg"><a href="'.$r['url_image'].'"><img src="'.$thumb.'" width="100" alt="author image"/></a></div>';
      }

      # TODO: abstraction for multiple links: key ('type/name') => value ('url')
      if (!empty($r['url_audio']) || !empty($r['url_misc']) || !empty($r['url_paper']) || !empty($r['url_slides']) || !empty($r['url_stream']))
        echo '<div class="flright">';
      if (!empty($r['url_paper']))
        echo '<a href="'.$r['url_paper'].'">Paper (PDF)</a>&nbsp;&nbsp;';
      if (!empty($r['url_slides']))
        echo '<a href="'.$r['url_slides'].'">Slides</a>&nbsp;&nbsp;';
      if (!empty($r['url_stream']))
        echo '<a href="'.$r['url_stream'].'">Video</a>&nbsp;&nbsp;';
      if (!empty($r['url_audio']))
        echo '<a href="'.$r['url_audio'].'">Audio</a>&nbsp;&nbsp;';
      if (!empty($r['url_misc']))
        echo '<a href="'.$r['url_misc'].'">Site</a>&nbsp;&nbsp;';
      if (!empty($r['url_audio']) || !empty($r['url_misc']) || !empty($r['url_paper']) || !empty($r['url_slides']) || !empty($r['url_stream']))
        echo '</div>';



      echo '('.$r['duration'];
      if (empty($r['duration'])) echo '??';
      if (!strstr($r['duration'], ':')) echo ' min';
      if ($r['type']=='i') echo ' - loop';
      echo ')&nbsp;<em>'; $i=0;
      foreach (fetch_authorids($db, $r['id']) as $user_id) {
        if ($i++>0) echo ', ';
        #echo $user_id.': ';
	echo xhtmlify($a_users[$user_id]);
      }
      echo '</em>';

      if ($location && !empty($r['location_id']))
      echo ' &raquo;&nbsp;Location: '.$a_locations[$r['location_id']];
      echo '<br/>';

      if ($details)
        echo '<div class="abstract">'.str_replace("\\n",'<br/>', xhtmlify($r['abstract'])).'</div>';
      echo '<hr class="psep"/>';
    }
  }

  function program_fieldset() {
?>
  <fieldset class="pdb">
    <input name="page" type="hidden" value="adminschedule" id="page"/>
    <input name="mode" type="hidden" value="" id="mode"/>
    <input name="sort" type="hidden" value="" id="sort"/>
    <input name="param" type="hidden" value="<?php echo $_REQUEST['param'];?>" id="param"/>
<?php
  }

  function dbadmin_unixtime($e, $start=true) {
    date_default_timezone_set('UTC');
    $time= strtotime((5+intval($e['day'])).' May 2011 '.$e['starttime'].':00 CEST');
    if (!$start && !strstr($e['duration'], ':'))
      $time = strtotime('+'.$e['duration'].'minutes', $time);
    return $time;
  }

  function dbadmin_orphans($db) {
    echo "<b>Pass 1: Scheduled Entries</b><br/>";
    $a_locations = fetch_selectlist($db, 'location');
    $q='SELECT * FROM activity ORDER BY day,strftime(\'%H:%M\',starttime)';
    $res=$db->query($q);
    if ($res) {
      $result=$res->fetchAll();
      foreach ($result as $r) {
        $err=0;
        if (!isset($a_locations[$r['location_id']]) || empty($a_locations[$r['location_id']])) {
          echo 'Event ('.$r['id'].') has no assigned location.<br/>'; 
          $err++;
        }
        if (count(fetch_authorids($db, $r['id'])) == 0) {
          echo 'Event ('.$r['id'].') has no assigned Author(s).<br/>'; 
          $err++;
        }

        if ($err) {
	  echo ' - ('.$r['id'].') <em>day</em>:'.$r['day'].', <em>start</em>:'.$r['starttime'].', <em>duration</em>:'.$r['duration'].', <em>type</em>:'.translate_type($r['type']).' <em>title</em>:'.limit_text($r['title']).'&nbsp;|&nbsp;';
          echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'edit\';document.myform.submit();">Edit</a>';
          if ($err==2) 
            echo '&nbsp;|&nbsp;<a class="active" onclick="if (confirm(\'Really delete Entry no. '.$r['id'].'?\')) {document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'delentry\';document.myform.submit();i}">Delete</a>';
          echo '<br/>';
          echo "\n";
        }
      }
      
    } else {
      echo '&nbsp;*&nbsp;Database query failed<br/>'."\n";
    }


    echo "<b>Pass 2: Persons</b><br/>";
    $q='SELECT id, name from user ORDER BY name;'; 
    $res=$db->query($q);
    if ($res) {
      $result=$res->fetchAll();
      foreach ($result as $r) {
        $aids=fetch_activity_by_author($db,$r['id']);
        if (count($aids)!=0) continue;
        echo '('.$r['id'].') "'.$r['name'].'" has no assignment.&nbsp;';
        echo '<a class="active" onclick="if (confirm(\'Really delete User no. '.$r['id'].'?\')) {document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'deluser\';document.myform.submit();i}">Delete</a>';
        echo '<br/>'."\n";
      }
    } else {
      echo '&nbsp;*&nbsp;Database query failed<br/>'."\n";
    }


    echo "<b>Pass 3: Locations</b><br/>";
    $q='SELECT id, name from location ORDER BY id;'; 
    $res=$db->query($q);
    if ($res) {
      $result=$res->fetchAll();
      foreach ($result as $r) {
        $aids=fetch_activity_by_location($db,$r['id']);
        if (count($aids)!=0) continue;
        echo '('.$r['id'].') "'.$r['name'].'" has no event assigned to it.&nbsp;';
        echo '<a class="active" onclick="if (confirm(\'Really delete Location no. '.$r['id'].'?\')) {document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'dellocation\';document.myform.submit();i}">Delete</a>';
        echo '<br/>'."\n";
      }
    } else {
      echo '&nbsp;*&nbsp;Database query failed<br/>'."\n";
    }

    echo "<b>Pass 4: Check registrations of Authors</b><br/>";
    echo "Take with a grain of salt. Spelling may slightly differ between here and the registration!<br/>";
    $cnt=array('tot' => 0, 'ok' => '0', 'part' => 0);
    list($regs,$list) = get_registrations();

    $q='SELECT id, name, email from user ORDER BY name;'; 
    $res=$db->query($q);
    $emailmissing='';
    if ($res) {
      echo '<ul>'."\n";
      $result=$res->fetchAll();
      foreach ($result as $r) {
        $cnt['tot']++;
        if (in_array($r['name'], $list)) { 
          $cnt['ok']++;
          continue;
        }
  	echo '<li>'.$r['name'].' is not yet registered';
        if (empty($r['email']))
  	  echo '<span style="color:red"> and we have no email address</span>';
        else 
          $emailmissing.=$r['name'].' &lt;'.$r['email'].'&gt;, ';
        echo '.';
        $pm=0;
        foreach (explode(' ',$r['name']) as $np) {
          if (strlen($np) <3) continue;
          if ($np== 'van') continue;
          foreach ($list as $n) {
            if (stristr($n, $np)) {
              echo '<br/>&nbsp;<span style="color:#aaaa00">Maybe: '.$n.'</span>?';
              $pm=1;
            }
          }
        }
        if ($pm) $cnt['part']++;
  	echo '</li>';
      }
      echo '</ul>'."\n";
    }
    echo '<p style="color:red">'.$cnt['ok'].' of '.$cnt['tot'].' Authors have registered.</p>';
    echo '<p style="color:#aaaa00">'.$cnt['part'].' partial matche(s).</p>';

    echo '<hr/>'."\n";
    echo '<p>Email of unregistered Authors:</p>'."\n";
    echo '<pre style="font-size:9px; background:#ccc; line-height:1.3em;margin-top:2em;">';
    echo wordwrap($emailmissing,100);
    echo '</pre><br/>'; 

    echo "<b>Pass 5: Check tagging of Author Registration</b><br/>";
    $checked=0;
    $good=0;
    if ($res) {
      foreach ($result as $r) {
        foreach ($regs as $n) {
          if ($r['name'] != $n['fullname']) continue;
          $checked++;
          if (empty($n['reg_vip'])) {
            echo 'Author: '.$r['name'].' is not yet marked as VIP in registration.<br/>';
          } else {
            $good++;
          }
        }
      }
    }
    echo '<p style="color:red">Checked '.$checked.' of '.$cnt['tot'].' Authors: good entries: '.$good.'</p>';
    
  }

  function get_registrations() {
    $dir = opendir(REGLOGDIR); 
    $rva = array(); 
    $rvn = array(); 
    while ($file_name = readdir($dir)) 
      if($file_name[0] != '.' && is_file(REGLOGDIR.$file_name)) {
        $v=parse_ini_file(REGLOGDIR.$file_name);
        $rva[]= array('first' => $v['reg_prename'], 'last' => $v['reg_name'], 'fullname' => $v['reg_prename'].' '.$v['reg_name'], 'reg_vip' => (isset($v['reg_vip'])?$v['reg_vip']:''));
        $rvn[]= $v['reg_prename'].' '.$v['reg_name'];
      }
    return array($rva, $rvn);
  }

  function dbadmin_checkconflicts($db) {
    echo "<b>Pass 1: List Incomplete Entries</b><br/>";
    $a_locations = fetch_selectlist($db, 'location');
    $q='SELECT * FROM activity ORDER BY day,strftime(\'%H:%M\',starttime)';
    $res=$db->query($q);
    $grrr=0;
    if ($res) {
      $result=$res->fetchAll();
      foreach ($result as $r) {
	$err=0;
        if (empty($r['day']) || $r['day'] < 1) {
          echo 'Event ('.$r['id'].') has no day set.<br/>'; 
	  $err++;
        }
        if (empty($r['starttime'])) {
          echo 'Event ('.$r['id'].') has no start-time set.<br/>'; 
	  $err++;
        }
        if (empty($r['duration']) && $r['type'] != 'c') {
          echo 'Event ('.$r['id'].') has no duration set.<br/>'; 
	  $err++;
        }
        if ($err) {
          $grrr++;
	  echo ' - ('.$r['id'].') <em>day</em>:'.$r['day'].', <em>start</em>:'.$r['starttime'].', <em>duration</em>:'.$r['duration'].', <em>type</em>:'.translate_type($r['type']).' <em>title</em>:'.limit_text($r['title']).'&nbsp;|&nbsp;';
          echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$r['id'].';document.getElementById(\'mode\').value=\'edit\';document.myform.submit();">Edit</a>';
          echo '<br/>'."\n";
        }
      }
    }
    if ($grrr==0) echo '&nbsp;*&nbsp;<span class="green">All OK.</span><br/>'."\n";
    else echo '&nbsp;*&nbsp;<span class="red"><b>'.$grrr.' incomplete entries found.</b></span><br/>'."\n";

    echo "<b>Pass 2: Locations Date/Time (Concerts are ignored)</b><br/>";
    $err=0;

    $q='SELECT * FROM activity ORDER BY day,strftime(\'%H:%M\',starttime)';
    $res=$db->query($q);
    if ($res) {
      $result=$res->fetchAll();
      foreach ($result as $a) {
        foreach ($result as $b) {
          if ($a['id'] == $b['id']) continue;
          if ($a['day'] != $b['day']) continue;
          if ($a['type'] == 'c' || $b['type']=='c') continue;
          if ($a['location_id'] != $b['location_id']) continue;
          if ($a['status'] == 0  || $b['status'] == 0) continue;
          $starta = dbadmin_unixtime($a);
          $enda   = dbadmin_unixtime($a, false);
          $startb = dbadmin_unixtime($b);
          if ( ($enda > $startb && $starta < $startb) 
             ||($starta == $startb) ) {
            $err++;
            echo '<span class="red">Conflict: ('.$a['id'].') ends in same location AFTER ('.$b['id'].') starts there.</span><br/>';
            echo ' - ('.$a['id'].') <em>day</em>:'.$a['day'].', <em>start</em>:'.$a['starttime'].', <em>duration</em>:'.$a['duration'].', <em>type</em>:'.translate_type($a['type']).' <em>title</em>:'.limit_text($a['title']).'&nbsp;|&nbsp;';
	    echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$a['id'].';document.getElementById(\'mode\').value=\'edit\';document.myform.submit();">Edit</a><br/>';
            echo ' - ('.$b['id'].') <em>day</em>:'.$b['day'].', <em>start</em>:'.$b['starttime'].', <em>duration</em>:'.$b['duration'].', <em>type</em>:'.translate_type($b['type']).' <em>title</em>:'.limit_text($b['title']).'&nbsp;|&nbsp;';
	    echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$b['id'].';document.getElementById(\'mode\').value=\'edit\';document.myform.submit();">Edit</a><br/>';
            echo "\n";
          }
        }
      }
    } else {
      echo '&nbsp;*&nbsp;Database query failed<br/>'."\n";
    }
    if ($err==0) echo '&nbsp;*&nbsp;<span class="green">No conflicts found.</span><br/>'."\n";
    else echo '&nbsp;*&nbsp;<span class="red"><b>'.$err.' conflict(s) found.</b></span><br/>'."\n";

    $err=0;
    echo "<b>Pass 3: Authors Date/Time</b><br/>";
    $q='SELECT id,name from user';
    $res=$db->query($q);
    if ($res) {
      $result=$res->fetchAll();
      foreach ($result as $u) {
        $aids=fetch_activity_by_author($db,$u['id']);
        if (count($aids)<2) continue;
        foreach ($aids as $a) {
	  foreach ($aids as $b) {
	    if ($a['id'] == $b['id']) continue;
	    if ($a['day'] != $b['day']) continue;
	    $starta = dbadmin_unixtime($a);
	    $enda   = dbadmin_unixtime($a, false);
	    $startb = dbadmin_unixtime($b);
	    if ($starta > $startb) continue;
            echo '<span class="yellow">Notice: '.$u['name'].' has more than one presentation on day '.$a['day'].'.</span><br/>';
	      echo ' - ('.$a['id'].') <em>day</em>:'.$a['day'].', <em>start</em>:'.$a['starttime'].', <em>duration</em>:'.$a['duration'].', <em>type</em>:'.translate_type($a['type']).' <em>title</em>:'.limit_text($a['title']).'&nbsp;|&nbsp;';
	      echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$a['id'].';document.getElementById(\'mode\').value=\'edit\';document.myform.submit();">Edit</a><br/>';
	      echo ' - ('.$b['id'].') <em>day</em>:'.$b['day'].', <em>start</em>:'.$b['starttime'].', <em>duration</em>:'.$b['duration'].', <em>type</em>:'.translate_type($b['type']).' <em>title</em>:'.limit_text($b['title']).'&nbsp;|&nbsp;';
	      echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$b['id'].';document.getElementById(\'mode\').value=\'edit\';document.myform.submit();">Edit</a><br/>';
	    if ($enda > $startb) {
              $err++;;
              echo '<span class="red">Conflict: '.$u['name'].' has overlapping presentations on day '.$a['day'].'!</span><br/>';
	      echo ' - ('.$a['id'].') <em>day</em>:'.$a['day'].', <em>start</em>:'.$a['starttime'].', <em>duration</em>:'.$a['duration'].', <em>type</em>:'.translate_type($a['type']).' <em>title</em>:'.limit_text($a['title']).'&nbsp;|&nbsp;';
	      echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$a['id'].';document.getElementById(\'mode\').value=\'edit\';document.myform.submit();">Edit</a><br/>';
	      echo ' - ('.$b['id'].') <em>day</em>:'.$b['day'].', <em>start</em>:'.$b['starttime'].', <em>duration</em>:'.$b['duration'].', <em>type</em>:'.translate_type($b['type']).' <em>title</em>:'.limit_text($b['title']).'&nbsp;|&nbsp;';
	      echo '<td><a class="active" onclick="document.getElementById(\'param\').value='.$b['id'].';document.getElementById(\'mode\').value=\'edit\';document.myform.submit();">Edit</a><br/>';
            }
	  }
        }
      }
    } else {
      echo '&nbsp;*&nbsp;Database query failed<br/>'."\n";
    }
    if ($err==0) echo '&nbsp;*&nbsp;<span class="green">No conflicts found.</span><br/>'."\n";
    else echo '&nbsp;*&nbsp;<span class="red"><b>'.$err.' conflict(s) found.</b></span><br/>'."\n";
  }

  function print_day($db, $num, $name, $details=true) {
    echo '<h2 class="ptitle">Day '.$num.' - '.$name.'</h2>';
    echo '<h3 class="ptitle">Main Track</h3>';
    echo '<div class="ptitle">Location: Bewerunge Room</div>'; ## XXX HARDCODED MAIN LOCATION
    query_out($db,
     'SELECT * FROM activity
      WHERE day='.$num.'
      AND ( type=\'p\' OR location_id=\'1\')
      ORDER BY strftime(\'%H:%M\',starttime)', $details, false, false
    );
    echo '<h3 class="ptitle">Workshops &amp; Events</h3>';
    echo '<div class="ptitle"></div>';
    query_out($db,
     'SELECT * FROM activity
      WHERE day='.$num.'
      AND NOT (type=\'p\' OR location_id=\'1\')
      ORDER BY typesort(type), strftime(\'%H:%M\',starttime), location_id', $details, true, true
    );
  }

  function print_filter($db) {
    $a_users = fetch_selectlist($db, 'user', 'ORDER BY name');
    $a_days = fetch_selectlist(0, 'days');
    $a_types = fetch_selectlist(0, 'types');
    $a_locations = fetch_selectlist($db, 'location');

    $a_locations[0]='-all-';
    $a_users[0]='-all-';
/*
    foreach($a_users as $i => &$a) {
      $a=limit_text($a,19); 
    }
*/

    $filter=array('user' => '0', 'day' => '0', 'type' => '0', 'location' => '0', 'id' => '0');

    if (1) { // filter enable
      if (isset($_REQUEST['pdb_filterday'])) $filter['day'] = intval(rawurldecode($_REQUEST['pdb_filterday']));
      if (isset($_REQUEST['pdb_filtertype'])) $filter['type'] = substr(rawurldecode($_REQUEST['pdb_filtertype']),0,1);
      if (isset($_REQUEST['pdb_filterauthor'])) $filter['user'] = intval(rawurldecode($_REQUEST['pdb_filterauthor']));
      if (isset($_REQUEST['pdb_filterlocation'])) $filter['location'] = intval(rawurldecode($_REQUEST['pdb_filterlocation']));
      if (isset($_REQUEST['pdb_filterid'])) $filter['id'] = intval(rawurldecode($_REQUEST['pdb_filterid']));
    }

    echo '<form action="index.php" method="post" name="myform">';
    echo '<fieldset class="pdb">';
    echo '<input name="page" type="hidden" value="program"/>';
    echo '<input name="mode" type="hidden" value="'.$_REQUEST['mode'].'"/>';
    if (isset($_REQUEST['details']))
      echo '<input name="details" type="hidden" value="'.$_REQUEST['details'].'"/>';
    echo '<legend>Filter:</legend>';
    echo '<label for="pdb_filterday">Day:</label>';
    echo '<select id="pdb_filterday" name="pdb_filterday" size="1" onchange="submit();">';
    gen_options(array_merge(array('0' => '-all-'), $a_days) , $filter['day']);
    echo '</select>&nbsp;'."\n";

    echo '<label for="pdb_filtertype">Type:</label>';
    echo '<select id="pdb_filtertype" name="pdb_filtertype" size="1" onchange="submit();">';
    gen_options(array_merge(array('0' => '-all-'), $a_types), $filter['type']);
    echo '</select>&nbsp;'."\n";

if (0) {
    echo '<label for="pdb_filterlocation">Location:</label>';
    echo '<select id="pdb_filterlocation" name="pdb_filterlocation" size="1" onchange="submit();">';
    gen_options($a_locations, $filter['location']);
    echo '</select>&nbsp;'."\n";
    #echo '<input class="smbutton" type="submit" title="Apply" value="Apply"/>&nbsp;';
}
if (1) {
#   echo '<br/>';
    echo '<label for="pdb_filterauthor">Author:</label>';
    echo '<select id="pdb_filterauthor" name="pdb_filterauthor" size="1" onchange="submit();">';
    gen_options($a_users , $filter['user']);
    echo '</select>&nbsp;'."\n";
    #echo '<span style="font-size:75%">Note: The \'filter by author\' will not be available in the final public version.</span>';
}
    echo '<input class="smbutton" type="submit" title="Apply" value="Apply"/>&nbsp;';


    echo '</fieldset>';
    echo '</form>';
    echo '<div style="margin-bottom:1em;">&nbsp;</div>';

    if ($filter['user'] != '0' || $filter['location'] != '0' || $filter['type'] != '0' || $filter['day'] != '0' || $filter['id'] != '0') return $filter;
    return 0;
  }

  function list_filtered_program($db,$filter,$details) {
    if ($filter['user'] != '0') 
      $q = 'SELECT DISTINCT activity.*
	    FROM activity,user,usermap
	    WHERE activity.id=usermap.activity_id AND user.id=usermap.user_id
	      AND user.id='.$filter['user'];
    else 
      $q='SELECT activity.* FROM activity WHERE 1=1';

    if ($filter['type'] != '0') $q.=' AND type='.$db->quote($filter['type']);
    if ($filter['day'] > 0) $q.=' AND day='.$db->quote($filter['day']);
    if ($filter['location'] > 0) $q.=' AND location_id='.$db->quote($filter['location']);
    if ($filter['id'] > 0) $q.=' AND id='.$db->quote($filter['id']);

    $order='';
    if ($order=='type')
      $q.=' ORDER BY day, typesort(type), strftime(\'%H:%M\',starttime), location_id;';
    else
      $q.=' ORDER BY day, strftime(\'%H:%M\',starttime), typesort(type), location_id;';

    query_out($db, $q, $details, $filter['type'] == '0',  $filter['location'] == '0', true);
  }

  function hardcoded_disclaimer() {
    echo '<div class="disclaimer center">The schedule is a major guideline. There is no guarantee events will take place at the announced timeslot.</div>';
  }

  function hardcoded_concert_and_installation_info($db) {
?>
<h2 class="ptitle pb">Concerts &amp; Installations</h2>
<div>
<p>The Concert lineup is not yet finalized (Mar 28, 2011).</p>
<?php /*  XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
During LAC 2011 there are two concerts, a clubnight and various fixed installations.<br/>

<h3>Concerts</h3>
<p>
<ul>
 <li>Saturday's opening concert starting 20:30 is at SETUP (Medialab) - <a href="http://www.setuputrecht.nl/">http://www.setuputrecht.nl/</a> - SETUP is above the ABN bank (Neude square #4)</li>
 <li>The concert on Sunday evening 20:30 takes place at the Kikker Theater - <a href="http://www.theaterkikker.nl/">http://www.theaterkikker.nl/</a> on <em>Ganzenmarkt 14</em>.</li>
 <li>Last but not least, the sound-night is on Monday 21:00 at SJU Jazzpodium - <a href="http://www.sjujazz.nl/">http://www.sjujazz.nl/</a> - <em>Varkenmarkt 2</em>.</li>
</ul>
<br/>
Furthermore LAC2010 features two Lunch-Concerts: both Sunday ("Live Demo of ChucK") &amp; Monday ("CSound Demo" and "Playing music lazily on the bank of a river while listening to the bells of a far away city") at 13:30 in SETUP (Medialab) - 3 mins walk from the conference venue.
<br/>
<br/>
Entrance:<br/>
SETUP (May 1) - free<br/>
Kikker Theater (May 2) - &euro; 7,50<br/>
SJU Jazzpodium (May 3) - &euro;7,50 Students: &euro; 5,- Tickets for SJU can be booked on-line.
</p>
<h3>Poster Session</h3>
<p>
Saturday and Sunday afternoon a poster-session will be hosted in Room 2.09.
</p>
<h3>Installations</h3>
<p>
There are fixed art installations in the upstairs installations rooms (3.17, 3.18, 3.19, 3.20) as well as in SETUP (Medialab):
</p>
<div style="padding:.5em 1em; 0em 1em">
<?php
  $q='SELECT activity.* FROM activity WHERE type='.$db->quote('i');
  $q.=' ORDER BY day, strftime(\'%H:%M\',starttime), typesort(type), location_id;';
	query_out($db, $q, false, false,  true, true);
 */

  echo '</div>'."\n";

  }


  function list_program($db,$details) {
    print_day($db, 1,'Friday',$details);
    print_day($db, 2,'Saturday',$details);
    print_day($db, 3,'Sunday',$details);
  }

  function table_program($db, $day, $print=false) {
    $a_days = fetch_selectlist(0, 'days');
    $a_locations = fetch_selectlist($db, 'location');
    $a_users = fetch_selectlist($db);
    $a_times = array(
                      '9:00', '10:00' , '10:15', '10:30', '10:45'
                    , '11:00' , '11:15', '11:30', '11:45'
                    , '12:00' , '12:15', '12:30', '12:45'
                    , '13:00' , '13:15', '13:30', '13:45'
                    , '14:00' , '14:15', '14:30', '14:45'
                    , '15:00' , '15:15', '15:30', '15:45'
                    , '16:00' , '16:15', '16:30', '16:45'
                    , '17:00' , '17:15', '17:30', '17:45'
                    , '18:00' 
               );

    if (!$print) {
      echo '<div style="float:right;">';
      for ($i=1; $i<4; $i++) {
	if ($i == $day) { echo 'Day '.$i.'&nbsp;&nbsp;'; continue;}
	echo '<a href="?page=program&amp;mode=table&amp;day='.$i.'">Day '.$i.'</a>&nbsp;&nbsp;';
      }
      echo '<a href="?page=program&amp;mode=table&amp;day=0">Concerts&amp;Installations</a>&nbsp;&nbsp;';
      echo '</div>';
    }

    echo '<h2 class="ptitle'.(($print && $day>1)?' pb':'').'">Day '.$a_days[$day].'</h2>';
    $q='SELECT DISTINCT location_id FROM activity WHERE day='.$day.'
        AND (type=\'p\' OR type=\'o\' OR type=\'w\' OR location_id=\'1\')
        ORDER BY location_id;';

    $res=$db->query($q);
    if (!$res) return; // TODO: print error msg
    $table=array();$i=0;
    $result=$res->fetchAll();
    foreach ($result as $c) {
      $table[$i]['loc']=$a_locations[$c['location_id']];
      $table[$i]['cskip']=0;
      $q='SELECT * FROM activity WHERE day='.$day.'
          AND (type=\'p\' OR type=\'o\' OR type=\'w\')
          AND location_id='.$c['location_id'].'
          ORDER BY strftime(\'%H:%M\',starttime);';
      $res=$db->query($q);
      if (!$res) return; // TODO: print error msg
      $stmt=$res->fetchAll();
      foreach ($stmt as $r) {
        if (empty($r['starttime'])) continue; 
        if ($r['status']==0) continue; # skip cancelled
        $table[$i][$r['starttime']]=$r;
      }
      $i++;
		}
    
    echo '<table cellspacing="0" class="ptb"><tr><th class="ptb">Time</th>';
    foreach ($table as $c) {
      echo '<th class="ptb">'.$c['loc'].'</th>';
    }
    echo '</tr>'."\n";

    foreach ($a_times as $t) {
      echo '<tr onmouseover="this.className=\'highlight\'" onmouseout="this.className=\'normal\'">';
      echo '<th class="ptb">'.$t.'</th>';
      foreach ($table as &$c) {
        if (isset($c[$t]) && is_array($c[$t])) {
          $d=$c[$t];
          #if ($c['cskip'] > 0) echo 'TIME CONFLICT!! '.$t.' @'.$c['loc'].'<br/>'; // XXX really list that here in plain view for users?
          if ($d['starttime'] == '9:00')
	    $c['cskip']=1;
	  else
			$c['cskip']=$d['duration']/15;
					$track=track_color($d); # tr0 - tr5
          echo '<td class="ptb'.($print?'':' active').' '.$track.'" rowspan="'.$c['cskip'].'"';
          if (!$print) echo ' onclick="showInfoBox('.$d['id'].');"';
          echo '>';
          #if ($d['status']==0) echo '<span class="red">Cancelled: </span>';
          #echo '<span'.(($d['status']==0)?' class="cancelled"':'').'><span>'.xhtmlify($d['title']).'</span></span>';
          echo '<span>'.xhtmlify($d['title']).'</span>';
          echo ' ('.$d['duration'].'mins)'; 
          echo '<div class="right"><em>'; $i=0;
          foreach (fetch_authorids($db, $d['id']) as $user_id) {
            if ($i++>0) echo ', ';
            echo xhtmlify($a_users[$user_id]);
          }
          echo '</em></div>';
          echo '</td>';
        } else if ($c['cskip'] == 0) {
          echo '<td class="ptb center">-</td>';
        } 

        if ($c['cskip']>0) {
          $c['cskip']--;
        }
      }
      echo '</tr>'."\n";
    }

		echo '</table>';

		echo track_legend();

    if (!$print) {
      echo '<div class="center">Concerts &amp; Installations are <b>not</b> included in this table.</div>';
      echo '<div id="dimmer" style="display:none;">&nbsp;</div>';
      echo '<div id="infobox" style="display:none;"><div class="center"><a class="active" onclick="hideInfoBox();">close</a></div><object id="infoframe" data="raw.php" type="application/xhtml+xml"><!--[if IE]><iframe id="ieframe" src="raw.php" allowtransparency="true" frameborder="0" ></iframe><![endif]--></object></div>';
    }
  }


	function export_progam_sv($db, $sep="\t") {
		# Table Header
		$rv='';
		$rv.= '"Start time"'.$sep;
		$rv.= '"End time"'.$sep;
		$rv.= '"Type"'.$sep;
		$rv.= '"Status"'.$sep;
		$rv.= '"Location"'.$sep;
		$rv.= '"Title"'.$sep;
		$rv.= '"Abstract"'.$sep;
		$rv.= '"Notes"'.$sep;
		$rv.= '"Author(s)"'.$sep;

		$rv.= "\n";

		# Table Body
		$a_locations = fetch_selectlist($db, 'location');
    $a_types = fetch_selectlist(0, 'types');

    $q='SELECT * FROM activity ORDER BY day, location_id, strftime(\'%H:%M\',starttime, typesort(type))';
    $res=$db->query($q);
    if (!$res) return; // TODO: print error msg
		$result=$res->fetchAll();

    foreach ($result as $r) {
      $rv.= '"'.iso8601($r).'"'.$sep;
      $rv.= '"'.iso8601($r,false).'"'.$sep;
			$rv.= '"'.($a_types[$r['type']]).'"'.$sep;
			$rv.= '"'.($r['status']&1?'confirmed':'cancelled').'"'.$sep;
			$rv.= '"'.($a_locations[$r['location_id']]).'"'.$sep;
			$rv.= '"'.trim($r['title']).'"'.$sep;
			$rv.= '"'.
				 str_replace("\r",'',
         str_replace("\n",'\n',
         str_replace('"','\"',
					trim($r['abstract'])
         ))).'"'.$sep;
			$rv.= '"'.
				 str_replace("\r",'',
         str_replace("\n",'\n',
         str_replace('"','\"',
					trim($r['notes'])
         ))).'"'.$sep;

			$rv.='"'; $authorcnt=0;


      foreach (fetch_authorids($db, $r['id']) as $user_id) {
				$ur=$db->query('SELECT * FROM user WHERE id ='.$user_id.';');
				if (!$ur) continue; ## TODO report error ?
				$ud=$ur->fetch(PDO::FETCH_ASSOC);

				if ($authorcnt++) $rv.=', ';
				$rv.=trim($ud['name']);
        if (!empty($ud['email']))
					$rv.=' ('.trim($ud['email']).')';
        if (!empty($ud['bio']))
					$rv.= ' ['.
						 str_replace("\r",'',
						 str_replace("\n",'\n',
						 str_replace('"','\"',
						  trim($ud['bio'])
					   ))).']';
			}
			$rv.= '"'.$sep;
			$rv.= "\n";
		}
		return $rv;
	}

  function vcal_program($db,$version='2.0',$raw=true) {
    if (!function_exists('quoted_printable_encode')) 
      require_once('quoted_printable.php');

    if ($version!='1.0' && $version!='2.0')
      $version='2.0';

    if ($raw) {
      ##header('Content-Type:text/calendar');
      header('Content-type: text/calendar; charset=utf-8');
      #header("Content-Type: text/x-vCalendar");
      header("Content-Disposition: inline; filename=lac2011.ics");
    }

    date_default_timezone_set('UTC');

    $a_users = fetch_selectlist($db);
    $a_locations = fetch_selectlist($db, 'location');

    $q='SELECT * FROM activity ORDER BY day, typesort(type), location_id, strftime(\'%H:%M\',starttime)';
    $res=$db->query($q);
    if (!$res) return; // TODO: print error msg
    $result=$res->fetchAll();
    echo 'BEGIN:VCALENDAR'."\r\n";
    echo 'VERSION:'.$version."\r\n"; 
    echo 'PRODID:-//linuxaudio.org/LAC2011//NONSGML v1.0//EN'."\r\n";

# XXX hardcoded concerts
    $result[] = array('id'=> 1000, 'day' => '1', 'starttime' => '20:30', 'duration' => '180',  'type' => 'c', 'title' => 'Opening Concert', 'abstract' => '', 'location_id' => 12, 'status' => '1');
    $result[] = array('id'=> 1001, 'day' => '2', 'starttime' => '20:30', 'duration' => '180',  'type' => 'c', 'title' => 'Concert', 'abstract' => '', 'location_id' => 13, 'status' => '1');
    $result[] = array('id'=> 1002, 'day' => '3', 'starttime' => '21:00', 'duration' => '240',  'type' => 'c', 'title' => 'Clubnight', 'abstract' => '', 'location_id' => 14, 'status' => '1');

    foreach ($result as $r) {
      if (empty($r['starttime'])) continue;
      if (empty($r['duration']) || $r['duration'] == 0 || strstr($r['duration'], ':')) continue;
      if ($r['status']==0) continue; // XXX cancelled

      echo 'BEGIN:VEVENT'."\r\n";
      echo 'UID:lac2011-'.$r['id'].'@linuxaudio.org'."\r\n";

      $dtstamp=filemtime('tmp/lac2011.db'); // XXX -> config.php
      echo 'DTSTAMP:'.date("Ymd\THis\Z", $dtstamp)."\r\n";  // optional

      foreach (fetch_authorids($db, $r['id']) as $user_id) {
        echo 'ATTENDEE;ROLE=CHAIR;CN='.trim($a_users[$user_id]).':MAILTO:no-reply@linuxaudio.org'."\r\n";
      }
      echo 'DTSTART:'.iso8601($r)."\r\n";
      echo 'DTEND:'.iso8601($r,false)."\r\n";
      if ($version=='2.0') {
        echo 'SUMMARY:LAC2011 - '.str_replace(',','\,',trim($r['title']))."\r\n";
        echo 'DESCRIPTION:'.str_replace("\r",'',str_replace(';','\;',str_replace(',','\,',str_replace("\n",'\n',trim($r['abstract'])))))."\r\n";
      } else {
        echo 'SUMMARY;ENCODING=QUOTED-PRINTABLE:LAC2011 - '.quoted_printable_encode(trim($r['title']))."\r\n";
        echo 'DESCRIPTION;ENCODING=QUOTED-PRINTABLE:'.quoted_printable_encode(str_replace("\n",'\n',trim($r['abstract'])))."\r\n";
      }
      if (!empty($r['location_id']) && $r['location_id'] > 0)
        echo 'LOCATION:'.trim($a_locations[$r['location_id']])."\r\n";

      echo 'CATEGORIES:'.translate_type($r['type'])."\r\n";
      #echo 'CATEGORIES:Ambisonics,whatever here, more there'."\r\n";
      echo 'END:VEVENT'."\r\n";
    }
    echo 'END:VCALENDAR'."\r\n";
  }
