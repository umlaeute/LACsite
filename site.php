<?php

#pages listed as 'tabs' on the site
  $pages = array(
    'about' => 'About',
    'participation' => 'Participation',
    'registration' => 'Registration',
    'participants' => 'Attendees',
    'travel' => 'Travel &amp; Stay',
    'sponsors' => 'Sponsors',
    'contact' => 'Contact'
  );

# other available pages - not shown as 'tabs'
  $hidden = array(
    'program' => 'Program',
    'download' => 'Download',
    'concerts' => 'Concerts',
    'news' => 'News',
  );

# don't list sponsors on these pages
  $nosponsors = array(
    'sponsors',
    'program',
    'upload',
  # 'download',
    'admin',
    'adminschedule',
    'registration',
    'regcomplete',
  );

#define sponsors/supportes
  $sponsors = array(
		'http://music.nuim.ie' => array('img' => 'img/logos/nuim.png', 'title' => 'NUIM'),
    'http://lwn.net/' => array('img' => 'img/logos/lwn.png', 'title' => 'LWN.NET'), 
    'http://linuxaudio.org/' => array('img' => 'img/logos/lao.png', 'title' => 'linuxaudio.org'),
    'http://www.citu.info/' => array('img' => 'img/logos/citu.png', 'title' => 'CiTu'),
  );

