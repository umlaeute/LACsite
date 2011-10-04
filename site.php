<?php

#pages listed as 'tabs' on the site
  $pages = array(
    'about' => 'About',
    'participation' => 'Participation',
    'registration' => 'Registration',
    'sponsors' => 'Sponsors',
    'travel' => 'Travel &amp; Stay',
    'contact' => 'Contact'
  );

# other available pages - not shown as 'tabs'
  $hidden = array(
    'download' => 'Download',
    'program' => 'Programme',
    'download' => 'Download',
    'participants' => 'Attendees',
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
    'http://stanford.edu/' => array('img' => 'img/logos/stanford.jpg', 'title' => 'Stanford.edu'),
    'http://linuxaudio.org/' => array('img' => 'img/logos/lao.png', 'title' => 'linuxaudio.org'),
  );
