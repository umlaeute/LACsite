<h1>Who is Who</h1>

<a href="img/LAC2012_group_picture.jpg">High quality version of the group picture</a>.

<div class="center">
<img src="img/LAC2012_group_picture_small.jpg" alt="group picture"/>
</div>
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
	'',
	'Matthias Geier',
	'', # 20
	'',
	'',
	'',
	'',
	'Oscar Pablo Di Liscia', # 25
	'José Rafael Subía Valdez (?)',
	'Zachary Berkowitz',
	'',
	'Joachim Heintz',
	'', # 30
	'Rui Nuno Capela',
	'',
	'',
	'',
	'IOhannes m zmölnig',
	'',
	'',
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
	'',
	'',
	'', # 50
	'Phillips Olajide',
);

echo '<ul class="multicolumn">'."\n";
for ($i=0; $i<51; $i++) {
	$n=($i%3)*17 + floor($i/3) +1;
	echo '<li>'.$n.' '.$pt[$n].'</li>'."\n";
}
echo "</ul>\n";
?>
