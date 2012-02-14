<?php
require_once('lib/userdb.php');
if (!defined('NL')) define('NL', "\n");


########

function render_list($head, $speakers) {
	if (count($speakers) <1 ) return;
	echo $head;
	echo '<table border="0" width="100%" id="speakers">'.NL.' <tr>';
  $cnt=0;
  foreach ($speakers as $s) {
    if ($cnt>0 && ($cnt%5 ==0)) {
      echo "</tr>\n <tr>\n";
    }
    echo "  <td>\n";
    echo '    <div class="portrait"><a href="'.local_url('speakers', 'uid='.$s['id']).'"';
    echo '     rel="person"><img src="'.usr_imgurl($s).'" alt="'.$s['name'].'"/></div>';
		echo $s['name'].'</a><br/>';
		if (!empty($s['tagline']))
			echo '<em>'.$s['tagline'].'</em>';
		else 
			echo '&nbsp;';
    echo "\n  </td>\n";
    $cnt++;
  }
  while ($cnt++%5 !=0) {
    echo '  <td><div class="portrait"></div></td>';
	}
	echo ' </tr>'.NL.'</table>'.NL;
}

function render_profile($s, $acts) {
	global $db;
	if (!is_array($s)) return; ## ERROR
	if (! (intval($s['id']) > 0)) return; ## ERROR
	programlightbox();

	# TODO : get talks of user (if applicable (flags)
	# compare to dbadmin_listall
	#fetch_user_activities($db, $speaker['id'])
	echo '<div class="user">';

	echo '<div class="portrait"><img src="'.usr_imgurl($s,200).'" alt="'.xhtmlify($s['name']).'"/></div>';

	echo '<div class="userinfo">';
	echo '<h3>'.$s['name'].'</h3>';
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
			echo xhtmlify(limit_text($r['title']));
			echo '</span>';
			# link to id, 
			echo ' - <span>'.translate_type($r['type']).'</span>';
			echo ' - <span>day:'.$r['day'].' - '.$r['starttime'].'</span>';

      if ($r['type']!='c') { ### all concerts same location --- lib/programdb.php:768
				if (!empty($r['location_id'])) {
					echo ' &raquo;&nbsp;Location: '.$a_locations[$r['location_id']];
				}
      }
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

########

if (isset($_REQUEST['uid']) && intval($_REQUEST['uid']) > 0 ) {
	$uid=intval($_REQUEST['uid']);
	render_profile(fetch_user($db, $uid), fetch_user_activities($db, $uid));
} else {
	render_list('<h2>Speakers</h2>', fetch_users($db, array('vip'=>1)) );
	render_list('<h2>Committee</h2>', fetch_users($db, array('vip'=>4)) );
	render_list('<h2>Organizers</h2>', fetch_users($db, array('vip'=>2)) );
}
