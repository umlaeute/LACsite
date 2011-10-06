<?php

#pages listed as 'tabs' on the site
  $pages = array(
    'about' => 'About',
    'participation' => 'Participate',
    'registration' => 'Registration',
    'participants' => 'Attendees',
    'sponsors' => 'Supporters',
    'travel' => 'Travel &amp; Stay',
    'contact' => 'Contact'
  );

# other available pages - not shown as 'tabs'
  $hidden = array(
    'files' => 'Download',
    'program'  => 'Agenda',
  );

# don't list sponsors on these pages
  $nosponsors = array(
    'upload',
    'admin',
    'adminschedule',
  );

#define sponsors/supportes
  $sponsors = array(
    'http://stanford.edu/' => array('img' => 'img/logos/stanford.jpg', 'title' => 'Stanford.edu'),
    'http://linuxaudio.org/' => array('img' => 'img/logos/lao.png', 'title' => 'linuxaudio.org'),
  );
