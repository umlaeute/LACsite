<h1>Registered Participants</h1>
<p>During registration you are given the option to include yourself here:</p>
<?php
$pfn=TMPDIR.'/lac2011.list';
if (file_exists($pfn)) {
	readfile($pfn);
} else { 
	echo 'No registered participants, yet.'; 
}


