<?php

#pages listed as 'tabs' on the site
  $pages = array(
    'about' => 'About',
    'participation' => 'Participate',
    'registration' => 'Registration',
    'sponsors' => 'Sponsoring',
    'travel' => 'Travel &amp; Stay',
    'contact' => 'Contact'
  );

# other available pages - not shown as 'tabs'
  $hidden = array(
    'download' => 'Download',
    'program' => 'Agenda',
    'download' => 'Download',
    'participants' => 'Attendees',
  );

# don't list sponsors on these pages
  $nosponsors = array(
    'admin',
    'adminschedule',
  );

#define sponsors/supportes
  $sponsors = array(
    'http://stanford.edu/' => array('img' => 'img/logos/stanford.jpg', 'title' => 'Stanford.edu'),
    'http://linuxaudio.org/' => array('img' => 'img/logos/lao.png', 'title' => 'linuxaudio.org'),
  );
