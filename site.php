<?php
# vim: ts=2 et
#default page
  $homepage='about';

#pages listed as 'tabs' on the site
  $pages = array(
    'about' => 'About',
    'registration' => 'Registration',
    'program'  => 'Schedule',
    'speakers'  => 'Delegates',
    'participants' => 'Attendees',
    'travel' => 'Travel &amp; Stay',
    'contact' => 'Contact',
    'sponsors' => 'Supporters',
  );

# other available pages - not shown as 'tabs'
  $hidden = array(
    'participation' => 'Participate',
    'files' => 'Download',
    'profile' => 'Profile',
    'excursion' => 'Excursion',
    'stream' => 'Stream',
  );

# don't list sponsors on these pages
  $nosponsors = array(
    'upload',
    'sponsors',
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
    'http://iem.kug.ac.at/' => array('img' => 'img/logos/iemlogo.png', 'title' => 'IEM'),
    'http://linuxaudio.org/' => array('img' => 'img/logos/lao.png', 'title' => 'linuxaudio.org'),
    'http://www.kug.ac.at/' => array('img' => 'img/logos/kug.png', 'title' =>
'University of Music, Graz'),
    'http://esc.mur.at/' => array('img' => 'img/logos/esc.png', 'title' => 'ESC im LABOR'),
    'http://elearning.tugraz.at/' => array('img' => 'img/logos/TUG.png', 'title' => 'TU Graz Dept. Social Learning'),
    'http://mur.at' => array('img' => 'img/logos/mur_at.png', 'title' => 'mur.at'),
    'http://auphonic.com' => array('img' => 'img/logos/auphonic.png', 'title' => 'auphonic'),
    'http://forum.mur.at' => array('img' => 'img/logos/forum_stadtpark.png', 'title' => 'Forum Stadtpark'),
    'http://pd-graz.mur.at' => array('img' => 'img/logos/pd~graz.png', 'title' => 'Pd~graz'),
    'http://vif.tugraz.at' => array('img' => 'img/logos/VirtualVehicle.jpg', 'title' => 'VIRTUAL VEHICLE Research Center'),
    'http://www.dismarc.org' => array('img' => 'img/logos/dismarc.jpg', 'title' => 'DISMARC'),
  );

  function clustermap() {
?>
    <div class="center">
<a href="http://www4.clustrmaps.com/counter/maps.php?url=http://lac.linuxaudio.org/2013/" id="clustrMapsLink" rel="external"><img src="http://www4.clustrmaps.com/counter/index2.php?url=http://lac.linuxaudio.org/2013/" style="border:0px;" alt="Locations of visitors to this page" title="Locations of visitors to this page" id="clustrMapsImg" />
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
