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
	'Joachim Heintz', 
	'Jonas Christensen (?)',
	'',
	'Kim Ervik',
	'Georg Holzmann',
	'Manuela Meier',
	'Matthias Kronlachner',
	'Bent Bisballe Nyeng',
	'Dominik Schmidt-Philipp',
	'Valentina Vuksic', # 10
	'',
	'Marian Weger',
	'Florian Faber',
	'Albert Gräf',
	'Fons Adriaensen', # 15
	'Ping',
	'Fernando Lopez Lezcano',
	'Rui Nuno Capela',
	'Marc Groenewegen',
	'Bruno Ruviaro', # 20
	'Frank Neumann',
	'Nils Gey',
	'Max Neupert',
	'',
	'Yen Tzu Chang', # 25
	'',
	'Li Chi Hsiao',
	'Funs Seelen',
	'Jakob Leben',
	'Henning Thielemann', # 30
	'Luigi Verona',
	'',
	'Philipp Überbacher',
	'Krzysztof Gawlas',
	'',
	'',
	'',
	'',
	'Peter Plessas',
	'Björn Lindig', # 40
	'Malte Steiner',
	'Clara Hollomey',
	'Chuckk Hubbard',
	'Charles Henry',
	'Thomas Mayr', # 45
	'Jeff Sandys',
	'Lars \'Muldjord\' Jensen',
	'',
	'Victor Lazzarini',
	'Steven Yi', # 50
	'Götz Dipper', 
	'Martin Schitter',
	'Flo Stöffelmayr',
	'',
	'Oscar Pablo di Liscia', # 55
	'Jörn Nettingsmeier',
	'Michal Seta',
	'Servando Barreiro',
	'IOhannes m, zmölnig',
	'', # 60
	'Harry van Haaren',
	'Alex Hofmann',
	'Kathi Vogt',
	'David Adler',
	'Peter Venus', # 65
	'Margarethe Maierhofer-Lischka',
	'Helene Hedsund',
	'David Garcia-Garzón',
	'João Pais',
	'Øyvind Brandtsegg', # 70
	'',
	'',
	'Marius Schebella',
	'Christian Pointner',
	'', # 75
	'',
	'Claude Heiland-Allen',
	'Jeremy Jongepier',
	'Vincent Rateau',
	'Olivia Suter', # 80
	'',
	'Birte Viermann',
	'',
	'Roman Haefeli',
	'Marwin', #85
	'',
	'Adrian Knoth',
	'Christina Clar',
	'Martin Rumori',
	'Natanael Olaiz', # 90
	'Sigurd Saue',
	'Florian Hollerweger',
	'Robin Gareus',
	'David Pirro',
	'Marije Baalman', # 95
	'Magnus Johansson',
	'Rob Canning',
	'Irina Hasnas',
	'Kyriakos Tsoukalas',
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
