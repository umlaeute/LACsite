<h1>LAC2012 Group Picture</h1>

<div class="center">
<img src="img/LAC2012_group_picture_small.jpg" alt="group picture"/>
</div>
<div class="center">
View <a href="img/LAC2012_group_picture.jpg">full-size</a> version of above group picture.
</div>
<h1>Who is..</h1>
<div class="center">
<img src="img/LAC2012_group_picture_ann.jpg" alt="annotated group picture"/>
</div>
<?php
$pt=array(
	'XXX', # 0
	'John Chowning', 
	'Dave Phillips',
	'',
	'',
	'Robin Gareus',
	'Steven Yi',
	'',
	'',
	'Aaron Heller',
	'', # 10
	'',
	'Miller Puckette',
	'',
	'Albert Gräf',
	'Giso Grimm (?)', # 15
	'Yann Orlarey',
	'Romain Michon',
	'Nils Peters',
	'Matthias Geier',
	'', # 20
	'',
	'',
	'',
	'',
	'Oscar Pablo Di Liscia', # 25
	'José Rafael Subía Valdez (?)',
	'Zachary Berkowitz',
	'Conor Curran',
	'Joachim Heintz',
	'', # 30
	'Rui Nuno Capela',
	'',
	'',
	'',
	'IOhannes m zmölnig',
	'',
	'Oded Ben-Tal',
	'',
	'',
	'Peter Plessas', # 40
	'Carr Wilkerson',
	'Marc Groenewegen',
	'Jörn Nettingsmeier',
	'Flávio Schiavoni',
	'Fernando Lopez-Lezcano', # 45
	'Bruno Ruviaro',
	'Harry van Haaren',
	'Jens Ahrens',
	'',
	'', # 50
	'Phillips Olajide',
	'Ping',
);

echo '<ul class="multicolumn nobullet">'."\n";
for ($i=0; $i<51; $i++) {
	$n=($i%3)*17 + floor($i/3) +1;
	echo '<li>'.$n.'. '.$pt[$n].'</li>'."\n";
}
echo '<li>&nbsp;</li><li>&nbsp;</li><li>52. <a href="https://ccrma.stanford.edu/~nando/ping/" rel="external">Ping</a></li>'."\n";
echo "</ul>\n";
echo '<div class="clearer"></div>'."\n";
echo '<p>If you are or know any of the unidentified persons, please <a href="'.local_url('contact').'">drop us a line</a>.</p>';
