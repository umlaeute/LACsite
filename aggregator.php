<?php

if (!defined('TMPDIR')) die();
if (empty($rpage) || empty($rcache)) die();

function filetimeout($filename, $timeout=600) {
  if(file_exists($filename)) {
    if (filemtime($filename) + $timeout < time()) {
      unlink($filename);
      return true;
    }
    return false;
  }
  return true;
}

function rx_cache() {
  global $rcache, $rpage; 

  $lockfile=$rcache.'.lock';
  if(!filetimeout($lockfile,60)) return;
  touch($lockfile); ### TODO flock() file to be sure!

  @$lines = file($rpage);
  if (!empty($lines)) {
    $f=fopen($rcache, "w");
    foreach ($lines as $l) fwrite($f, $l);
    fclose($f);
  }
  unlink($lockfile);
}

function tx_cache() {
  global $rcache, $rpage; 
  include ($rcache); flush();
  $lpage=preg_replace('@_export/xhtmlbody/@','', $rpage);
  echo '<div class="wikilink">visit <a href="'.$lpage.'" rel="external">'.$lpage.'</a> to edit this page</div>';
}

# if cached && last_touch < 300 s -> return cache
# if cached && last_touch > 300 s & last_touch < 3600 -> return cache & load
# if last_touch > 3600 || otherwise (not cached) ->  load & return it

$sent=false;
@$mtime=filemtime($rcache) or 0;

if ($war && file_exists($rcache) && time() < 3600 + $mtime) {
  tx_cache();
  $sent=true;
}

if (!file_exists($rcache) || time() > 300 + $mtime) {
  rx_cache();
}

if (!$sent && file_exists($rcache)) {
  tx_cache();
  $sent=true;
}

if (!$sent) {
  echo 'This page is temporarily not available. Please visit the original at';
  echo '<a href="'.$lpage.'">'.$lpage.'</a>.';
}

