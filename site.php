<?php

#pages listed as 'tabs' on the site
  $pages = array(
    'repercussions' => 'About',
    'program' => 'Programme',
    'download' => 'Download',
    'participants' => 'Attendees',
    'sponsors' => 'Sponsors',
    'contact' => 'Contact'
  );

# other available pages - not shown as 'tabs'
  $hidden = array(
    'about' => 'About',
    'registration' => 'Registration',
    'download' => 'Download',
    'participation' => 'Participation',
    'travel' => 'Travel &amp; Stay',
//  'concerts' => 'Concerts',
//  'news' => 'News',
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
    'http://music.nuim.ie' => array('img' => 'img/logos/nuim.jpg', 'title' => 'NUIM'),
    'http://lwn.net/' => array('img' => 'img/logos/lwn.jpg', 'title' => 'LWN.NET'), 
    'http://www.sonokinetic.net/' => array('img' => 'img/logos/sonokinetic.jpg', 'title' => 'Sonokinetic'),
    'http://linuxaudio.org/' => array('img' => 'img/logos/lao.png', 'title' => 'linuxaudio.org'),
    'http://www.citu.info/' => array('img' => 'img/logos/citu.png', 'title' => 'CiTu'),
  );
