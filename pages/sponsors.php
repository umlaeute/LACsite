<?php /* OLD PRE-CONFERENCE
<h1>Sponsoring</h1>
<p>
As admittance to the conference is free, several things need sponsoring.
If you want to contribute to the conference and want to know what you
can sponsor and what we offer in return, please contact the
conference organisation: lac -at- linuxaudio -dot- org 
</p>
*/?>
<h1>Sponsors</h1>
<p>
As admittance to the conference was free, several things required sponsoring which was made possible by the following partners:<br/> <br/>
</p>

<table border="0" width="100%" id="supporter">
<tr>
<?php
  $cnt=0;
  foreach ($sponsors as $sl => $si) {
    if ($cnt>0 && ($cnt%4 ==0)) {
      echo "</tr>\n<tr>\n";
    }
    echo "  <td>\n";
    echo '    <a href="'.$sl.'"'."\n";
    echo '     rel="supporter"><img src="'.$si['img'].'" title="'.$si['title'].'" alt="'.$si['title'].'"/><br/>';
    echo $si['title']."</a>\n  </td>\n";
    $cnt++;
  }
  while ($cnt++%4 !=0) {
    echo '  <td>
    <a href="mailto:lac@linuxaudio.org">Your logo could be here</a>
  </td>';
  }

?>
</tr>
</table>
