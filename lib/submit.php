<?php
if (!defined('REGLOGDIR')) die();

function checkEmail($email) {
  if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)){
    list($username,$domain)=explode('@',$email);
    if(checkdnsrr($domain.'.','MX')) return true;
  }
  return false;
}

function checkreg() {
  global $errmsg;
  $err=0;
  if (isset($_POST['reg_name']) && strlen($_POST['reg_name']) < 2) {
    $errmsg.='<p class="error">* Please give your family name.</p>'."\n";
    $err|=1;
  }

  if (isset($_POST['reg_prename']) && strlen($_POST['reg_prename']) < 2) {
    $errmsg.='<p class="error">* Please enter your given name(s).</p>'."\n";
    $err|=1;
  }

  if (isset($_POST['reg_country']) && $_POST['reg_country'] == '') {
    $errmsg.='<p class="error">* Please specify your country.</p>'."\n";
    $err|=1;
  }

  if (isset($_POST['reg_email']) && !checkEmail($_POST['reg_email'])) {
    $errmsg.='<p class="error">* Please specify a vaild email address.</p>'."\n";
    $err|=1;
  }

  if (isset($_POST['reg_email_confirm']) && $_POST['reg_email_confirm']!='') {
    $errmsg.='<p class="error">Hello Spam Bot :) Human users: Please leave the reg_email_confirm field empty.</p>'."\n";
    $err|=1;
  }

  if (check_max_submit_per_ip() !== true) {
    # TODO: save IP-address; allow max 5 req per IP / day.
    $errmsg.='<p class="error">* We have already accepted 3 submissions from your IP address in the last 24 hours. Please try again tomorrow or contact us by email for group-registrations.</p>'."\n";
    $err|=1;
  }

  return (isset($_POST['reg_name']) && $err==0);
}

function format_registration($a) {
  return '
  Name        : '.rawurldecode($a['reg_name']).'
  First Name  : '.rawurldecode($a['reg_prename']).'
  Tagline     : '.rawurldecode($a['reg_tagline']).'
  Email       : '.rawurldecode($a['reg_email']).'
  Age         : '.preg_replace('/A(\d{2})(.{2})/','${1}-${2}',rawurldecode($a['reg_agegroup'])).'
  Country     : '.rawurldecode($a['reg_country']).'
  Profession  : '
  .(!isset($a['reg_profession'])?'??':(
    rawurldecode($a['reg_profession'])
  )).'
  Proceedings : '
  .(rawurldecode($a['reg_proceedings'])?'yes':'no')     .'
  Public List : '
  .(rawurldecode($a['reg_whoelselist'])?'yes':'no')     .'
  Uses Linux  : '
  .((isset($a['reg_useathome']) && rawurldecode($a['reg_useathome']))?'at home ':'_not_ at home ')
  .((isset($a['reg_useatwork']) && rawurldecode($a['reg_useatwork']))?'at home ':'_not_ at work ')
  .'
  Pro Audio   : '
  .(!isset($a['reg_audiopro'])?'??':(
     ((rawurldecode($a['reg_audiopro'])==1)?'no':'')
    .((rawurldecode($a['reg_audiopro'])==2)?'yes':'')
  )).'
  Interests   : '
# TODO use pages/registration.php -> $about array
#.($a['reg_vmusician']?'Musician or composer, ':'')
#.($a['reg_vdj']?'DJ, ':'')
#.($a['reg_vswdeveloper']?'Software Developer, ':'')
#.($a['reg_vhwdeveloper']?'Hardware Developer, ':'')
#.($a['reg_vswuser']?'Software User, ':'')
#.($a['reg_vmediapro']?'Media Professional, ':'')
#.($a['reg_vmproducer']?'Music Producer, ':'')
#.($a['reg_vvproducer']?'Video Producer, ':'')
#.($a['reg_vresearcher']?'Researcher, ':'')
#.($a['reg_vpress']?'Press, ':'')
#.($a['reg_vinterested']?'Just interested, ':'')
#.($a['reg_vother']?'Other ':'')
  .rawurldecode($a['reg_about'])
.'
  Notes       : '.rawurldecode($a['reg_notes']).'
'
.(isset($a['reg_vip'])?'  VIP         : '.$a['reg_vip']:'');
}

function sanevalue($s) {
  $l = 150;
  if (strlen($s)<=$l) return ($s);
  return (substr($s,0,$l).'..');
}

function savereg() {
  global $config;
  $name = preg_replace('/[^a-zA-Z0-9]/','_', $_POST['reg_prename'].'_'.$_POST['reg_name']);
  $fname = REGLOGDIR .date("Ymd_His").'-'.$name.'.ini';
  $handle = fopen($fname, "a");
  if (!$handle) {
    global $errmsg;
    $errmsg.='<p class="error">Error in saving your registration data. We apologize for the inconvenience.<br/>';
    $errmsg.='Please inform us about your problem by email.</p>';
    return false;
  }
  $datafields=array(
    'reg_name', 'reg_prename', 'reg_tagline', 'reg_email', 'reg_agegroup', 
    'reg_country', 'reg_useathome', 'reg_useatwork', 'reg_audiopro',
#    'reg_vmusician', 'reg_vdj', 'reg_vswdeveloper',
#    'reg_vhwdeveloper', 'reg_vswuser', 'reg_vpress',
#    'reg_vmediapro', 'reg_vmproducer', 'reg_vvproducer', 'reg_vresearcher',
#    'reg_vinterested', 'reg_vother',
    'reg_profession', 'reg_about', 
    'reg_proceedings', 'reg_whoelselist', 'reg_notes'
  );

  #store in .ini file format -> human readable and 
  #parseable with PHP's parse_ini_file()
  fwrite($handle, '; Registration for user: '.$name."\n");
  foreach ($datafields as $k) {
    if (!isset($_POST[$k])) $val='';
    else $val=rawurldecode($_POST[$k]);
    fwrite($handle, $k.'="'.preg_replace('/[";]/','.',$val)."\"\n");
  }
  fwrite($handle, "\n");
  fclose($handle);

  # send email to organizers
  $subject=SHORTTITLE.' registration: '.preg_replace('/[^a-zA-Z0-9 ,]/','', $_POST['reg_prename'].' '.$_POST['reg_name']);
  $message='Dear '.SHORTTITLE.' Organizers,

A new participant has registered. Here are the details:
'.format_registration($_POST).'

  Browser used: '.$_SERVER['HTTP_USER_AGENT'].'

-- 
This mail was generated by '.CANONICALURL.'/

';

# $message.=print_r(parse_ini_file($fname),true);
  $headers = 'From: '.$config['mailfrom'];

  if (!empty($config['mailto'])) 
    mail($config['mailto'], $subject, wordwrap($message,70), $headers);

# send email to participant
  $subject=SHORTTITLE.' registration';
  $rcpt=rawurldecode($_POST['reg_email']);

  $message='Dear '.$_POST['reg_prename'].',

Your registration for '.SHORTTITLE.' has been received. 

Should you have any questions, want to modify or cancel your registration let us know by replying to this email.
Looking forward to seeing you in Stanford in April '.LACY.'.

'.format_registration($_POST).'

-- 
This mail was generated by '.CANONICALURL.'/

';

  if (isset($config['regmail']) && $config['regmail'] === true) 
    mail($rcpt, $subject, wordwrap($message,70), $headers);

  log_ip_address();

  if (rawurldecode($_POST['reg_whoelselist']))
    add_public_listing();

  return true; 
}


function add_public_listing() {
  try {
    $db=new PDO("sqlite:tmp/reg".LACY.".db"); // XXX -> config.php
  } catch (PDOException $exception) {return;}

  $q='INSERT into pubrg (name, prename, tagline) VALUES ('
    .' '.$db->quote(sanevalue(rawurldecode($_POST['reg_name'])))
    .','.$db->quote(sanevalue(rawurldecode($_POST['reg_prename'])))
    .','.$db->quote(sanevalue(rawurldecode($_POST['reg_tagline'])))
    .');';
  $db->exec($q);
  write_public_listing();
}

function write_public_listing() {
  try {
    $db=new PDO("sqlite:tmp/reg".LACY.".db"); // XXX -> config.php
  } catch (PDOException $exception) {return;}

  $q='SELECT * from pubrg ORDER BY name, prename';
  $res=$db->query($q);
  if (!$res) return;
  $newlist="<ul>\n";
  $result=$res->fetchAll();
  foreach ($result as $c) {
    $newlist.='<li>';
    $newlist.=xhtmlify($c['name']).', ';
    $newlist.=xhtmlify($c['prename']);
    if (!empty($c['tagline'])) {
      $newlist.=' &mdash; ';
      $newlist.=xhtmlify($c['tagline']);
    }
    $newlist.="</li>\n";
  }
  $newlist.='</ul>';
  proc_public_listing($newlist);
}

function proc_public_listing($data) {
  $tfn=TMPDIR.'/lac'.LACY.'-reg.lock';
  $pfn=TMPDIR.'/lac'.LACY.'-reg.list';
  $timeout = 100;
  $fp = fopen($tfn, "a");
  while ($timeout-- > 0) {
    if (flock($fp, LOCK_EX|LOCK_NB)) {
      $dp = fopen($pfn, "w");
      fwrite($dp, $data);
      fclose($dp);
      flock($fp, LOCK_UN);
      break;
    } else {
      usleep(round(rand(10, 100)*1000));
    }
  }
  fclose($fp);
}

function log_ip_address() {
  try {
    $db=new PDO("sqlite:tmp/reg".LACY.".db"); // XXX -> config.php
  } catch (PDOException $exception) {return;}

  $q='INSERT into iplog (ip_addr, regname) VALUES ('
    .' '.$db->quote($_SERVER['REMOTE_ADDR'])
    .','.$db->quote(sanevalue(rawurldecode($_POST['reg_prename'])).' '.sanevalue(rawurldecode(isset($_POST['pdb_name'])?$_POST['pdb_name']:'')))
    .');';
  $db->exec($q);
}

function check_max_submit_per_ip() {
  try {
    $db=new PDO("sqlite:tmp/reg".LACY.".db"); // XXX -> config.php
  } catch (PDOException $exception) {return true;}

  $q='SELECT COUNT(*) from iplog WHERE'
    .' ip_addr='.$db->quote($_SERVER['REMOTE_ADDR'])
    .' AND datetime(ac_time,\'+'.(3600*24).' seconds\') > datetime(\'now\')'
    .';';

  $res=$db->query($q);
  if (!$res) return true;
  $s=$res->fetch();
  if ($s[0] > 2) return false;

  return true;
}