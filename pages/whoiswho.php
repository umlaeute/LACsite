<h1>LAC 2013 Group Picture</h1>

<div class="center">
<img src="img/LAC2013_group_small.jpg" alt="group picture"/>
</div>
<div class="center">
View <a href="img/LAC2013_group.jpg">full-size</a> version of above group picture.
</div>
<h1>Who is..</h1>
<div class="center">
<img src="img/LAC2013_group_annotated.jpg" alt="annotated group picture"/>
</div>
<?php
$pt=array(
	'XXX', # 0
	'', 
	'',
	'',
	'',
	'',
	'',
	'',
	'',
	'',
	'', # 10
	'',
	'',
	'',
	'',
	'', # 15
	'',
	'',
	'',
	'',
	'', # 20
	'',
	'',
	'',
	'',
	'', # 25
	'',
	'',
	'',
	'',
	'', # 30
	'',
	'',
	'',
	'',
	'',
	'',
	'',
	'',
	'',
	'', # 40
	'',
	'',
	'',
	'',
	'', # 45
	'',
	'',
	'',
	'',
	'', # 50
	'', 
	'',
	'',
	'',
	'',
	'',
	'',
	'',
	'',
	'', # 60
	'',
	'',
	'',
	'',
	'', # 65
	'',
	'',
	'',
	'',
	'', # 70
	'',
	'',
	'',
	'',
	'', # 75
	'',
	'',
	'',
	'',
	'', # 80
	'',
	'',
	'',
	'',
	'',
	'',
	'',
	'',
	'',
	'', # 90
	'',
	'',
	'',
	'',
	'', # 95
	'',
	'',
	'',
	'',
	'', # 100


);

echo '<ul class="multicolumn nobullet">'."\n";
$numheads=99;
for ($i=0; $i<$numheads; $i++) {
	$n=($i%3)*floor($numheads/3) + floor($i/3) +1;
	echo '<li>'.$n.'. '.$pt[$n].'</li>'."\n";
}
echo "</ul>\n";
echo '<div class="clearer"></div>'."\n";
echo '<p>If you know - or are - any of the unidentified persons, please <a href="'.local_url('contact').'">drop us a line</a>.</p>';
#echo '<p>The picture was taken right after Dave Phillips\' <a href="'.local_url('program','pdb_filterid=27').'">keynote</a> address, not everybody that came to the conference is in this picture. Sorry, we won\'t <em><a href="http://www.gimp.org/" rel="external">gimp</a></em> you in, your next chance will be LAC\'13 at IEM, Graz.</p>';
