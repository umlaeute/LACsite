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

  function clustermap() {
?>
    <div class="center">
<a href="http://www4.clustrmaps.com/counter/maps.php?url=http://lac.linuxaudio.org/2012/" id="clustrMapsLink" rel="external"><img src="http://www4.clustrmaps.com/counter/index2.php?url=http://lac.linuxaudio.org/2012/" style="border:0px;" alt="Locations of visitors to this page" title="Locations of visitors to this page" id="clustrMapsImg" />
</a>
<script type="text/javascript">
function cantload() {
img = document.getElementById("clustrMapsImg");
img.onerror = null;
img.src = "http://www2.clustrmaps.com/images/clustrmaps-back-soon.jpg";
document.getElementById("clustrMapsLink").href = "http://www2.clustrmaps.com";
}
img = document.getElementById("clustrMapsImg");
img.onerror = cantload;
</script>
    </div>
<?php
  }
