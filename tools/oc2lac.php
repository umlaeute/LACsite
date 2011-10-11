<?php

define('YEAR','2012'); 
define('BASEDIR','/home/sites/lac.linuxaudio.org/'.YEAR); 

### INPUT DATABASE (openconf)
# require(BASEDIR.'openconf/config.php'); # not readable:
define("OCC_DB_USER", 'lac'.YEAR);
define("OCC_DB_PASSWORD", "XXX");
define("OCC_DB_HOST", "localhost");
define("OCC_DB_NAME", "openconf".YEAR);
#

### OUTPUT DATABASE (lac website)
#define('PDOPRGDB','sqlite:'.BASEDIR.'/docroot/tmp/lac'.YEAR.'.db');
define('PDOPRGDB','sqlite:/tmp/lac2011.db');


### All systems go
$db=new PDO(PDOPRGDB);
$ocdb=new PDO('mysql:host='.OCC_DB_HOST.';dbname='.OCC_DB_NAME, OCC_DB_USER, OCC_DB_PASSWORD); 

function oc_query($q) {
  global $ocdb;
  #echo "DEBUG Q: $q\n";
  $res=$ocdb->query($q);
  if ($res) return ($res->fetchAll());
  return false;
}

function lac_query($q, $mode='assoc') {
  global $db;
  $res=$db->query($q);
  if (!$res) return false;
  if ($mode==false) {
    return ($res->fetchAll());
  }
  return ($res->fetch(PDO::FETCH_ASSOC));
}

function lac_exec($q) {
  global $db;
  #echo "DEBUG Q: $q\n";
  if ($db->exec($q)) 
    return $db->lastInsertId();
  return false;
}

# get papers and fill info
$papers=oc_query('SELECT DISTINCT * from paper where accepted="Accept";');
$px=array();
foreach ($papers as $p) {
  $topics=oc_query('SELECT * from topic join papertopic on papertopic.topicid = topic.topicid where papertopic.paperid='.$p['paperid'].';');
  $p['mytopics'] = $topics;
  $authors=oc_query('SELECT * from author where paperid='.$p['paperid'].' ORDER BY position;');
  $p['myauthors'] = $authors;
  $px[]=$p;
  #print_r($p);
}
$papers=$px; unset($px);
#print_r(count($papers)); exit;

foreach (oc_query('SELECT DISTINCT * from author join paper on paper.paperid=author.paperid where paper.accepted="Accept";') as $a) {
  #echo "inert user:". $a['name_first'].' '.$a['name_last']."\n";
  $rv=lac_exec('insert into user (name, bio, email) VALUES('
    .$db->quote($a['name_first'].' '.$a['name_last']).','
    .$db->quote($a['organization']).','
    .$db->quote($a['email'])
    .');');
  if ($rv===false) {
    echo "insert user:". $a['name_first'].' '.$a['name_last']."\n";
    echo " !!! ERROR ADDING NEW user : ".$a['email']."\n";
    print_r($db->errorInfo());
  } 
}

#print_r(lac_query('SELECT * from user;', false));
echo "-----\n";

foreach ($papers as $p) {
  #echo "paper :".$p['title']."\n";
  $actid=lac_exec('insert into activity (title, type, abstract, notes, url_paper, duration, location_id) VALUES('
    .$db->quote($p['title']).','
    .$db->quote('p').','
    .$db->quote($p['abstract']).','
    .$db->quote($p['pcnotes'].' -- '.$p['keywords'].' -- '.$p['contactemail'].' -- '.$p['contactphone'].' -- '.$p['lastupdate']).','
    .$db->quote('http://lac.linuxaudio.org/2011/papers/'.$p['paperid'].'.'.$p['format'])
    .',45,1' # duration, location
    .');');
  #echo "DEBUG: activity: $actid\n";
  if ($actid===false) {
    echo " !!! ERROR INSERTING activity: ".$p['title']."\n";
    print_r($db->errorInfo());
    continue;
  }
  # loop over authors for this paper
  foreach ($p['myauthors'] as $a) {
    # loopup lac-authorid
    $lacaid = lac_query('SELECT id from user where email='.$db->quote($a['email']).';');
    if ($lacaid===false) {
      echo " !!! ERROR LOOKING UP user : ".$a['email']."\n";
      continue;
    }
    $rv=lac_exec('insert into usermap (activity_id, user_id) VALUES('
      .intval($actid).','
      .intval($lacaid['id'])
      .');');
    if ($rv===false) {
      echo "author: ".$a['email'].' ('.intval($lacaid['id']).') -> activity: '.$actid."\n";
      echo " !!! ERROR creating user-map.\n";
      print_r($db->errorInfo());
    } 
  }
}
echo "OK\n";
