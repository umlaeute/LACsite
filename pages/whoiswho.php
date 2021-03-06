<h1>LAC 2013 Group Picture</h1>

<div class="center">
<a href="img/LAC2013_group.jpg"><img src="img/LAC2013_group_small.jpg" alt="group picture"/></a>
</div>
<h1>Who is..</h1>
<div class="center">
<a href="img/LAC2013_group_annotatedMEDIUM.jpg"><img src="img/LAC2013_group_annotated.jpg" alt="annotated group picture"/></a>
</div>
<?php
$pt=array(
	'XXX', # 0
	'Joachim Heintz', 
	'Jonas Suhr Christensen',
	'Wolfgang Leitner',
	'Kim Ervik',
	'Georg Holzmann',
	'Manuela Meier',
	'Matthias Kronlachner',
	'Bent Bisballe Nyeng',
	'Dominik Schmidt-Philipp',
	'Valentina Vuksic', # 10
	'Christoph Kuhr',
	'Marian Weger',
	'Florian Faber',
	'Albert Gräf',
	'Fons Adriaensen', # 15
	'Ping',
	'Fernando Lopez-Lezcano',
	'Rui Nuno Capela',
	'Marc Groenewegen',
	'Bruno Ruviaro', # 20
	'Frank Neumann',
	'Nils Gey',
	'Max Neupert',
	'',
	'Yen Tzu Chang', # 25
	'Jakob Leben',
	'Li Chi Hsiao',
	'Funs Seelen',
	'',
	'Henning Thielemann', # 30
	'Luigi Verona',
	'Egor Sanin',
	'Philipp Überbacher',
	'Krzysztof Gawlas',
	'Bernhard Hampel-Waffenthal', #35
	'Guido Scholz',
	'Peter Bubestinger',
	'',
	'Peter P.',
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
	'Bernhard Bleier',
	'Oscar Pablo di Liscia', # 55
	'Jörn Nettingsmeier',
	'Michal Seta',
	'Servando Barreiro',
	'IOhannes m zmölnig',
	'Erik Scholz', # 60
	'Harry van Haaren',
	'Jan Jacob Hofmann',
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
	'Gernot Lettner', # 75
	'',
	'Claude Heiland-Allen',
	'Jeremy Jongepier',
	'Vincent Rateau',
	'Olivia Suter', # 80
	'Markus Seeber',
	'Birte Viermann',
	'Hartmut Noack',
	'Roman Haefeli',
	'Marvin', #85
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
$missing=0;
for ($i=0; $i<$numheads; $i++) {
	$n=($i%3)*floor($numheads/3) + floor($i/3) +1;
	$name=$pt[$n];
	if (''==$name)$missing++;
	echo '<li>'.$n.'. '.$name.'</li>'."\n";
}
echo "</ul>\n";
echo '<div class="clearer"></div>'."\n";
echo '<p>If you know - or are - any of the '.$missing.' unidentified persons (or notice some other irregularities), please <a href="'.local_url('contact').'">drop us a line</a>.</p>';
echo '<p>The picture was taken right after the closing ceremony on the third (of four) days. Not everybody that came to the conference is in this picture. Sorry, we won\'t <em><a href="http://www.gimp.org/" rel="external">gimp</a></em> you in, your next chance will be LAC\'14 at ZKM, Karlsruhe.</p>';
