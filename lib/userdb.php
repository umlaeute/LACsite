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

function usr_imgurl($u,$size=128) {
	if (!empty($u['url_image'])) {
		// TODO check for valid URL! prevent abuse.
		return $u['url_image'];
	}
	if (!empty($u['email'])) {
		$grhash=md5(strtolower(trim($u['email'])));
		return 'http://gravatar.com/avatar/'.$grhash.'?s='.$size.'&amp;d=mm';
	}
	return 'http://gravatar.com/avatar/0?f=y&amp;s='.$size.'&amp;d=mm'; ## TODO local mystery man image.
}

function usr_sendhash($uid) {
	# check that no access-key has been sent
	# in the last hour.
	$q='SELECT udate from auth WHERE user_id='.$uid.';';

	$res=$db->query($q);
	if (!$res) { say_db_error('usr_sendhash'); return 1;}
	$d = $res->fetch(PDO::FETCH_ASSOC);

	if (0) { # TODO date comparison
		return 1;
	}

	# create hash
	$ukey='ABC';

	# save in DB
	$q='UPDATE auth set ukey='.$db->quote($ukey).', udate=date() WHERE user_id='.$uid.';';
	if ($db->exec($q) !== 1){
		# error
		return 1;
	}
	# send email to user
	$msg='';
	$msg.='';
	$msg.=local_url('profile', 'ukey='.rawurlencode($ukey));
	return 0;
}
