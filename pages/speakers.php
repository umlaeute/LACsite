<?php
require_once('lib/userdb.php');

if (isset($_REQUEST['uid']) && intval($_REQUEST['uid']) > 0 ) {
	$uid=intval($_REQUEST['uid']);
	render_profile(fetch_user($db, $uid), fetch_user_activities($db, $uid));
} else {
	render_list('<h2>Speakers</h2>', fetch_users($db, array('vip'=>1)) );
	render_list('<h2>Artists</h2>', fetch_users($db, array('vip'=>8)) );
	render_list('<h2>Committee</h2>', fetch_users($db, array('vip'=>4)) );
	render_list('<h2>Organizers</h2>', fetch_users($db, array('vip'=>2)) );
}
