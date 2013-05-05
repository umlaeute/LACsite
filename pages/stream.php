<?php

    echo '<h1>Conference Schedule</h1>'."\n";
### Note during conference about streaming and IRC ###
    echo '<div class="center" style="margin-top:.5em; margin-bottom:.-5em;"><p>During the conference, live A/V streams are available for the main track.</p><p>Remote participants are invited to join <a href="http://webchat.freenode.net/?channels=lac2013" rel="external">#lac2013 on irc.freenode.net</a>, to be able to take part in the discussions, ask questions, and get technical assistance in case of stream problems.</p><p>Conference Material can be found on the <a href="'.local_url('files').'">Download Page</a>.</p><br/></div>';
    echo '<iframe src="http://lac-live.spreadspace.org/lac/embed.php" width="874px" height="600px" frameborder=0 padding=0 margin=0>';
    #echo '<iframe src="streamsrc.php" style="width:100%; height:12em; border:0px;"></iframe>';
    #echo '<div innerHTML="streamsrc.php" style="width:100%; border:0px;">';
    #require_once('streamsrc.php');
    echo '</div>';

    echo '<br/><hr/>'."\n";

### Note before conference about streaming and IRC ###

  hardcoded_disclaimer();

# vim: ts=2 et
