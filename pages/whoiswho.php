<h1>LAC 2012 Group Picture</h1>

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
	'Stephen Pope',
	'Frank Ekeberg',
	'Robin Gareus',
	'Steven Yi',
	'',
	'Michael Hoeldke',
	'Aaron Heller',
	'', # 10
	'Egor Sanin',
	'Miller Puckette',
	'Tracy Hytry',
	'Albert Gräf',
	'Giso Grimm', # 15
	'Yann Orlarey',
	'Romain Michon',
	'Nils Peters',
	'Matthias Geier',
	'', # 20
	'Jeff Sandys',
	'Mike McCrea',
	'',
	'Michael Wilson',
	'Oscar Pablo Di Liscia', # 25
	'José Rafael Subía Valdez (?)',
	'Zachary Berkowitz',
	'Conor Curran',
	'Joachim Heintz',
	'Alexia Massalin', # 30
	'Rui Nuno Capela',
	'',
	'',
	'Juan Reyes',
	'IOhannes m zmölnig',
	'Juan Pampin',
	'Oded Ben-Tal',
	'Steve Batte',
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
	'Anne Swanberg (?)',
	'Marika Swanberg (?)', # 50
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
echo '<p>If you know - or are - any of the unidentified persons, please <a href="'.local_url('contact').'">drop us a line</a>.</p>';
