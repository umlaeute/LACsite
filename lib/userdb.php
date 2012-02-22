<?php
require_once('lib/programdb.php');
if (!defined('NL')) define('NL', "\n");

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

function fetch_user($db, $uid, $force=false) {
	$q='SELECT * from user WHERE id='.intval($uid); 
	if (!$force) $q.=' AND (flags&1)=1;';
	$q.=';';
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

function usr_sanitize_imgurl($url) {
	if (empty($url)) return '';

	$err=0;
	$url=trim(htmlspecialchars($url,ENT_QUOTES));

	if (!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)) $err=1;

	if ($err) {
		echo '<div class="dbmsg">The specified URL "'.$url.'" is not acceptable.</div>'."\n";
		return '';
	}
	return $url;
}

function usr_imgurl($u,$size=128) {
	if (!empty($u['url_image'])) { return $u['url_image']; }
	if (!empty($u['email'])) {
		$grhash=md5(strtolower(trim($u['email'])));
		return 'http://gravatar.com/avatar/'.$grhash.'?s='.$size.'&amp;d=mm';
	}
	return 'http://gravatar.com/avatar/0?f=y&amp;s='.$size.'&amp;d=mm'; ## TODO local mystery man image.
}

function usr_auth_ukey($db, $ukey, $email='') {
	$q='SELECT user_id from auth,user WHERE auth.ukey = '.$db->quote($ukey);
	if(!empty($email))
		$q.=' AND auth.user_id = user.id AND user.email='.$db->quote($email);
	$q.=';';
	$res=$db->query($q);
	if (!$res) { say_db_error('usr_auth_ukey'); return -1;}
	$d = $res->fetch(PDO::FETCH_ASSOC);
	return $d['user_id'];
}

function usr_get_uid($db, $email) {
	if (empty($email)) { return -1;}
	$q='SELECT id from user WHERE email = '.$db->quote($email).';'; 
	$res=$db->query($q);
	if (!$res) { say_db_error('usr_get_uid'); return -1;}
	$d = $res->fetch(PDO::FETCH_ASSOC);
	return $d['id'];
}

function usr_sendhash($db, $uid) {
	# check that no access-key has been sent in the last hour or so.
	# TODO: remember 3 last times - over 24 hours.
	$q='SELECT udate from auth WHERE user_id='.$uid.' AND DATETIME(udate,\'+300 seconds\') > DATETIME() ;';

	$res=$db->query($q);
	if (!$res) { say_db_error('usr_sendhash'); return 1;}
	$d = $res->fetch(PDO::FETCH_ASSOC);

	if (isset($d['udate'])) {
		return 2;
	}

	$q='SELECT * from user WHERE id='.$uid.';';
	$res=$db->query($q);
	if (!$res) { say_db_error('usr_sendhash get user info'); return 1;}
	$d = $res->fetch(PDO::FETCH_ASSOC);

	# create hash
	$ukey=preg_replace("/[ ]/e",'chr(array_search(mt_rand(0, 62) ,array_flip(array_merge(range(48, 57), range(65, 90), range(97, 122)))))', str_repeat(" ", 12));

	# TODO: check for hash conflicts.

	# save in DB
	$q='INSERT OR REPLACE INTO auth (user_id, ukey, udate) VALUES ('.$uid.','.$db->quote($ukey).', DATETIME());';
	if ($db->exec($q) !== 1){
		say_db_error('usr_sendhash save-key');
		return -2;
	}

	# send email to user
  global $config;
  $headers = 'From: '.$config['mailfrom'];
  $subject=SHORTTITLE.' user-profile';
  $rcpt=$d['email'];
	$msg='';
	$msg.='Dear '.$d['name'].',

You receive this email because you are either speaker, artist, conference-committee member or otherwise involved with the '.SHORTTITLE.'.

The conference website allows you to create a publicly visible personal profile (biography, links to sites, mugshot, etc.).

Providing this information is optional, yet we\'d like to encourage you to introduce yourself and help to give the conference a face.

To edit or update your profile, please go to:
  ';
	$msg.=canonical_url('profile', 'ukey='.rawurlencode($ukey),'&');
	$msg.='
or log-in manually using your email-address and the password 
  '.$ukey.' at '.canonical_url('profile').'

If you have any questions or comments, don\'t hesitate and reply to this email.

-- 
This mail was generated by '.CANONICALURL.'

';
	mail($rcpt, $subject, wordwrap($msg,70), $headers);
	return 0;
}

function usr_msg_sendhash($db, $uid, $email='') {
	global $config;
	switch (usr_sendhash($db, $uid)) {
		case 0:
			if (!empty($empty))
				echo '<div class="dbmsg">An email has been sent to your account ('.$email.'). Please follow the link given in the email to edit your profile.</div>'."\n";
			else
				echo '<div class="dbmsg">Invitation sent to uid:'.$uid.'.</div>'."\n";
			return 0;
			break;
		case 2:
			echo '<div class="dbmsg">You already requested a new access-token in the last 5 minutes.</div>'."\n";
			break;
		default:
			echo '<div class="dbmsg">An error occured. Please try again. If the problem presists, contact '.$config['txtemail'].'</div>'."\n";
			break;
	}
	return -1;
}

########

function render_list($head, $speakers) {
	if (count($speakers) <1 ) return;
	echo $head;
	echo '<table border="0" width="100%" class="speakers">'.NL.' <tr>';
  $cnt=0;$posttds='';
  foreach ($speakers as $s) {
    if ($cnt>0 && ($cnt%5 ==0)) {
      echo "</tr>\n <tr>\n";
      echo $posttds; $posttds='';
      echo "</tr>\n <tr>\n";
    }
    echo "  <td>\n";
    echo '    <div class="portrait"><a href="'.local_url('speakers', 'uid='.$s['id']).'"';
    echo '     rel="person"><img src="'.usr_imgurl($s).'" alt="'.$s['name'].'"/></a></div>';
    $posttds.='<td class="tag"><div><a href="'.local_url('speakers', 'uid='.$s['id']).'" rel="person">'.$s['name'].'</a></div>';
    if (!empty($s['tagline']))
      $posttds.='<div class="tagline">'.xhtmlify($s['tagline']).'</div>';
    else 
      $posttds.='&nbsp;';
    $posttds.='&nbsp;</td>';
    echo "\n  </td>\n";
    $cnt++;
  }
  while ($cnt++%5 !=0) {
    echo '  <td><div class="portrait"></div></td>';
	}
	echo "</tr>\n <tr>\n";
	echo $posttds;
	echo ' </tr>'.NL.'</table>'.NL;
}

function render_profile($s, $acts) {
	global $db;
	if (!is_array($s)) return; ## ERROR
	if (! (intval($s['id']) > 0)) return; ## ERROR
	programlightbox();

	echo '<div class="user">';

	echo '<div class="portrait"><img src="'.usr_imgurl($s,200).'" alt="'.xhtmlify($s['name']).'"/></div>';

	echo '<div class="userinfo">';
	echo '<h3>'.xhtmlify($s['name']).'</h3>';
	if (!empty($s['tagline']))
		echo '<h4>Tagline/Affiliation</h4><p>'.xhtmlify($s['tagline']).'</p>';
	if (!empty($s['url_person']) || !empty($s['url_institute']) || !empty($s['url_project'])  ) {
		echo '<h4>Links</h4><ul>';
		if (!empty($s['url_person'])) echo '<li><a href="'.$s['url_person'].'" rel="external">'.$s['url_person'].'</a></li>';
		if (!empty($s['url_institute'])) echo '<li><a href="'.$s['url_institute'].'" rel="external">'.$s['url_institute'].'</a></li>';
		if (!empty($s['url_project'])) echo '<li><a href="'.$s['url_project'].'" rel="external">'.$s['url_project'].'</a></li>';
		echo '</ul>';
	}
	if (is_array($acts) && count ($acts) >0) {
		$a_locations = fetch_selectlist($db, 'location');
		echo '<h4>Session(s)</h4>';
		echo '<ul>'.NL;
		echo '<li><a href="'.local_url('program','pdb_filterauthor='.$s['id'].'&amp;mode=list&amp;details=1').'">Show [all] in program.</a></li>';
		foreach ($acts as $r) {
			echo '<li class="active" onclick="showInfoBox('.$r['id'].');">';
			echo '<span'.(($r['status']==0)?' class="cancelled"':'').'>';
			echo xhtmlify($r['title']);
			echo '</span>';
			# link to id, 
			echo '<br/>&raquo;&nbsp;<span>'.translate_type($r['type']).'</span>';
			if ($r['type'] != 'c' && $r['location_id'] != '3')
			echo ' - <span>day:'.$r['day'].' - '.translate_time($r['starttime']).'</span>';

      //if ($r['type']!='c') { ### all concerts same location --- lib/programdb.php:768
				if (!empty($r['location_id'])) {
					echo '<br/>&raquo;&nbsp;Location: '.$a_locations[$r['location_id']];
				}
      //}
			echo '</li>'.NL;
		}
		echo '</ul>'.NL;
	}
	echo '</div>'.NL;

	echo '<div class="clearer"></div>';
	echo '<div class="bio"><p>'.str_replace("\n",'<br/>',xhtmlify($s['bio'])).'</p></div>';

	#echo '<pre>';
	#print_r($speaker);
	#print_r($activities);
	#echo '</pre>';
	echo '</div>';
}
