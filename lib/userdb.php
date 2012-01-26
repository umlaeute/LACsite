<?php
require_once('lib/programdb.php');

function fetch_users($db, $filter=array()) {
	$q='SELECT * from user WHERE 1'; 
	$q.=' AND (flags&1)=1';
	if (isset($filter['vip']))
		$q.=' AND (vip&'.$filter['vip'].')!=0';
	$q.=' ORDER BY name;'; 
	$res=$db->query($q);
	if (!$res) { say_db_error('fetch_users'); return array();}
	return $res->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_user($db, $uid) {
	$q='SELECT * from user WHERE id='.intval($uid).';'; 
	$res=$db->query($q);
	if (!$res) { say_db_error('fetch_user'); return NULL;}
	return $res->fetch(PDO::FETCH_ASSOC);
}

function fetch_user_activities($db, $uid) {
	$q='SELECT DISTINCT activity.*
		FROM activity,user,usermap 
		WHERE activity.id=usermap.activity_id AND user.id=usermap.user_id
		AND user.id='.intval($uid);
	$q.=' ORDER BY day, strftime(\'%H:%M\',starttime), typesort(type), location_id;'; 
	$res=$db->query($q);
	if (!$res) { say_db_error('fetch_users'); return array();}
	return $res->fetchAll(PDO::FETCH_ASSOC);
}
