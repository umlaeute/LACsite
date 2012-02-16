<?php
# vim: ts=2 et
#default page
  $homepage='about';

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
    'profile' => 'Profile',
    'program'  => 'Agenda',
    'speakers'  => 'Speakers',
    'profile' => 'Profile',
  );

# don't list sponsors on these pages
  $nosponsors = array(
    'upload',
    'admin',
    'adminschedule',
  );

#pages that require authentication
  $adminpages = array(
    'upload',
    'admin',
    'adminschedule',
  );

#define sponsors/supportes
  $sponsors = array(
    'http://ccrma.stanford.edu/' => array('img' => 'img/logos/ccrma.png', 'title' => 'CCRMA'),
    'http://lwn.net/' => array('img' => 'img/logos/lwn.jpg', 'title' => 'LWN.NET'),
    'http://fedoraproject.org/' => array('img' => 'img/logos/fedora_infinity.png', 'title' => 'Fedora Project'),
    'http://arts.stanford.edu/sai.php?section=sica&amp;page=about' => array('img' => 'img/logos/sica.jpg', 'title' => 'SiCA, Stanford'),
    'http://www.stanford.edu/' => array('img' => 'img/logos/stanford.jpg', 'title' => 'Stanford University'),
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
