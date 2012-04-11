<?php

function stripstreamtable($t) {
	return str_replace(
		'+0000','UTC',
		str_replace(
		'<a href=','<a rel="external" href=',
		str_replace(
		'colspan="7"','colspan="8"',
		preg_replace('@up since.*, @','',
		preg_replace('@</?table[^>]*>@','',$t)))));
}

function printstreamheader() {
	echo '<tr>'."\n";
	echo '<th>URL</th>'."\n";
	echo '<th>Description</th>'."\n";
	echo '<th>Geometry</th>'."\n";
	echo '<th>FPS</th>'."\n";
	echo '<th>A bit/s</th>'."\n";
	echo '<th>V bit/s</th>'."\n";
	echo '<th>Listeners</th>'."\n";
	echo '<th>up since</th>'."\n";
	echo '</tr>'."\n";
}

function streamtable() {
	$src0=file_get_contents('http://ccrma.stanford.edu:8080/lac2012.xsl');
	$src1=file_get_contents('http://streamer.stackingdwarves.net/lac2012.xsl');
	echo '<table class="streaminfo" style="width:100%;font-size:11px;line-height:17px;">'."\n";
	printstreamheader();
	echo stripstreamtable($src0);
	echo stripstreamtable($src1);
	echo '</table>'."\n";
}

streamtable();
