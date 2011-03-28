<?php

# run e.g `php program2ff.php > /tmp/lac2011_db.sh`

if (isset($_SERVER['HTTP_HOST'])) {
	        die('this script should be run locally only.');
}

require_once('config.php');
require_once('lib.php');
require_once('programdb.php');


function fetch_authors($db, $activity_id) {
	global $db;
	$rv = array();
	$q='SELECT name from user join usermap on user.id = usermap.user_id where usermap.activity_id='.$activity_id.';'; 
    $res=$db->query($q);
	if (!$res) return $rv;

	$result=$res->fetchAll();
	foreach ($result as $r) {
		$rv[]=$r['name'];
	}
	return $rv;
}

function ffescape($s) {
	return trim(str_replace('"', '\\"', $s));
}

function filetitle($s) {
	return preg_replace("@__*@", '_', preg_replace("@[^A-Za-z0-9_-]*@", '', str_replace(' ', '_', trim($s))));
}

$q='SELECT id,title,day,starttime FROM activity ORDER BY day, strftime(\'%H:%M\',starttime), typesort(type), location_id;';
$res=$db->query($q);
if (!$res) { 
	echo 'DATABASE ERROR: '.print_r($db->errorInfo(),true)."\n";
	die();
}
$result=$res->fetchAll();
$i=0;
foreach ($result as $r) {
	$i++;
	$r['authors'] = join(fetch_authors($db, $r['id']),', ');
	$tme=dbadmin_unixtime($r);
	date_default_timezone_set('CET');
	$stm=date("Hi", $tme);
	if (0) {
		# HUMAN copy/paste
		echo '# day: '.$r['day'].'  time: '.$r['starttime'].'  id: '.$r['id']." \n";
		echo '  --title "'.ffescape($r['title']).'" \\'."\n";
		echo '  --artist "'.ffescape($r['authors']).'" \\'."\n";
		echo '  --date "'.date("Y-m-d H:i:s", $tme).'" \\'."\n";
		echo '# "day'.$r['day'].'_'.$stm.'_'.filetitle($r['title']).'.ogv"'."\n";
		echo "\n";
	} else {
		# BASH EVAL
		echo 'VAR'.$i.'=\'META="';
		echo ' --title \\"'.ffescape($r['title']).'\\"';
		echo ' --artist \\"'.ffescape($r['authors']).'\\"';
		echo ' --date \\"'.date("Y-m-d H:i:s", $tme).'\\"';
		echo '"; ID='.$r['id'].'; OUTFILE="day'.$r['day'].'_'.$stm.'_'.filetitle($r['title']).'.ogv"\'';
		echo "\n";
	}
}
echo "LASTID=$i\n";

#http://lac.linuxaudio.org/2010/recordings/day1_1215_Emulating_a_Combo_Organ.ogv