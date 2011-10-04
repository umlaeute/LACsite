<?php

if (!defined('REGLOGDIR')) die();

$mode='';
if (isset($_POST['mode'])) $mode=rawurldecode($_POST['mode']);

switch ($mode) {
  case 'vip_author':
		$mode='vip';
		$vip='author';
		break;
  case 'vip_organizer':
		$mode='vip';
		$vip='organizer';
		break;
	case 'vip_none':
		$mode='vip';
		$vip='';
		break;
	default:
		break;
}

adminpage();
switch ($mode) {
  case 'csv':
    $handle = fopen(TMPDIR.'/registrations.csv', "w");
    fwrite($handle, export_sv(","));
    fclose($handle);
    echo 'Download: <a href="download.php?file=registrations.csv">registrations.csv</a>';
    break;
  case 'email':
    $r=scan_registrations();
    echo '<pre style="font-size:9px; background:#ccc; line-height:1.3em;">';
    echo wordwrap(list_emails($r),100);
    echo '</pre><br/>'; 
    show_fields($r,'reg_email');
    break;
  case 'badgespdf':
    $r=scan_registrations();
    gen_badges_pdf($r);
    echo '<div style="height:1em;">&nbsp;</div>';
    echo 'Download: <a href="download.php?file=lac2012badges.pdf">lac2012badges.pdf</a>';
    break;
  case 'badgestex':
    $r=scan_registrations();
    echo '<pre style="font-size:9px; background:#ccc; line-height:1em;">';
    echo gen_badges_source($r);
    echo '</pre>'; 
    break;
  case 'remarks':
    $r=scan_registrations();
    show_fields($r,'reg_notes');
    break;
  case 'proceedings':
    $r=scan_registrations();
    $v=count_fields($r,'reg_proceedings');
    echo '<p>Got '.$v.' requests for proceedings out of '.count($r).' total registrations.</p>';
    show_fields($r,'reg_proceedings');
    break;
  case 'food':
    $r=scan_registrations();
    show_fields($r,'reg_food');
    break;
  case 'detail':
    show_registration($_POST['param']);
    break;
	case 'vip':
		if (isset($vip))
			set_vip($_POST['param'], $vip);
  case 'list':
    $r=scan_registrations();
    echo '<p>We have '.count($r).' registered participants:</p>';
    echo '<table class="adminlist" cellspacing="0">'."\n";
    foreach ($r as $f) {
      echo '<tr><td style="border-bottom: dotted 1px;">';
			echo substr($f, 16);
			echo '</td><td>';
      echo '<span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'detail\';document.myform.submit();">Show Details</span>';
			echo '</td><td>';

			$filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $f).'.ini';
			$v=parse_ini_file(REGLOGDIR.$filename);

			if (!isset($v['reg_vip'])) { $v['reg_vip']=''; }
      switch(strtolower($v['reg_vip'])) {
        case 'author':
					echo '<td><span style="font-weight:bold;">[Author]</span></td>';
					echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_organizer\';document.myform.submit();">Organizer</span></td>';
					echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_none\';document.myform.submit();">No-VIP</span></td>';
          break;
        case 'organizer':
					echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_author\';document.myform.submit();">Author</span></td>';
					echo '<td><span style="font-weight:bold;">[Organizer]</span></td>';
					echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_none\';document.myform.submit();">No-VIP</span></td>';
          break;
        default:
					echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_author\';document.myform.submit();">Author</span></td>';
					echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_organizer\';document.myform.submit();">Organizer</span></td>';
					echo '<td><span>[No-VIP]</span></td>';
          break;
			}
			echo '</td></tr>'."\n";
    }
    echo '</table>';
    break;
  default:
    break;
}

function adminpage() {
  echo '
<form action="index.php" method="post" name="myform">
';
  admin_fieldset();
  echo '
    <input name="page" type="hidden" value="admin" id="page"/>
    <input name="mode" type="hidden" value="" id="mode"/>
		<input name="param" type="hidden" value="" id="param"/>
</form>
<div style="height:1em;">&nbsp;</div>
';
#  print_r($_POST); # XXX
}

function scan_registrations() {
  $dir = opendir(REGLOGDIR); 
  $filearray = array(); 
  while ($file_name = readdir($dir)) 
    if($file_name[0] != '.' && is_file(REGLOGDIR.$file_name))
      $filearray[] = preg_replace('/\.ini$/','',$file_name);
  return $filearray;
}

function show_registration($fn) {
  $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
  echo '<p>File: '.$fn.'</p>';
  echo '<pre style="font-size:9px;">';
  echo wordwrap(format_registration(parse_ini_file(REGLOGDIR.$filename)), 100);
  echo '</pre>';
}

function set_vip($fn, $vip='author') {
	$filename=$name = REGLOGDIR.preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
	# TODO flock file  ?

	#remove previous reg_vip (if any)
	$sh=fopen($filename, 'r');
	if (!$sh) {
		return false;
	}
	$th=fopen($filename.'.tmp', 'w');
	if (!$th) {
    fclose($sh);
		return false;
	}
	while (!feof($sh)) {
    $line=fgets($sh);
    if (strpos($line, 'reg_vip')===false) {
			fwrite($th, $line);
    }
  }
  fclose($sh);
	if (!empty($vip)) {
		fwrite($th, 'reg_vip="'.preg_replace('/[";]/','.',$vip)."\"\n");
	}
  fclose($th);
  #delete old source file
  unlink($filename);
  #rename target file to source file
  rename($filename.'.tmp', $filename);
}

function count_fields($f, $k) {
  $cnt=0;
  foreach ($f as $fn) {
    $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
    $v=parse_ini_file(REGLOGDIR.$filename);
    if ($v[$k]) $cnt++;
  }
  return $cnt;
}

function show_fields($f, $k) {
  echo "<ul>\n";
  foreach ($f as $fn) {
    $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
    $v=parse_ini_file(REGLOGDIR.$filename);
    if (!empty($v[$k])) {
      echo '<li style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($fn).'\';document.getElementById(\'mode\').value=\'detail\';document.myform.submit();">';
      echo $v['reg_prename'].' '.$v['reg_name'].': '.$v[$k].'</li>'."\n";
    }
  }
  echo "</ul>\n";
}

function list_emails($f) {
  $rv='';
  foreach ($f as $fn) {
    $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
    $v=parse_ini_file(REGLOGDIR.$filename);
    $rv.=$v['reg_email'].', ';
  }
  return $rv;
}

function export_sv($sep="\t") {
  $rv='';
  $rv.= '"Last Name"'.$sep;
  $rv.= '"First Name"'.$sep;
  $rv.= '"Tagline"'.$sep;
  $rv.= '"Email"'.$sep;
  $rv.= '"Age"'.$sep;
  $rv.= '"County"'.$sep;
  $rv.= '"Using Linux"'.$sep;
  $rv.= '"Profi"'.$sep;
  $rv.= '"Interests"'.$sep;
  $rv.= '"Profession"'.$sep;
  $rv.= '"Proceedings"'.$sep;
  $rv.= '"Public reg."'.$sep;
  $rv.= '"VIP"'.$sep;
  $rv.= '"Notes"'."\n";

  $r=scan_registrations();

  foreach ($r as $fn) {
    $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
    $v=parse_ini_file(REGLOGDIR.$filename);

    $rv.= '"'.$v['reg_name'].'"'.$sep;
    $rv.= '"'.$v['reg_prename'].'"'.$sep;
    $rv.= '"'.$v['reg_tagline'].'"'.$sep;
    $rv.= '"'.$v['reg_email'].'"'.$sep;
    $rv.= '"'.preg_replace('/A(\d{2})(.{2})/','${1}-${2}',$v['reg_agegroup']).'"'.$sep;
    $rv.= '"'.$v['reg_country'].'"'.$sep;
    $rv.= '"'.($v['reg_useathome']?'at home, ':'').($v['reg_useatwork']?'at work':'').'"'.$sep;
    $rv.= '"'
	    .(($v['reg_audiopro']==1)?'no':'')
	    .(($v['reg_audiopro']==2)?'yes':'')
	    .(($v['reg_audiopro']==0)?'??':'');
    $rv.= '"'.$sep;
    $rv.= '"';
    $rv.= ($v['reg_vmusician']?'Composer or musician, ':'');
    $rv.= ($v['reg_vdj']?'DJ, ':'');
    $rv.= ($v['reg_vswdeveloper']?'Software devel, ':'');
    $rv.= ($v['reg_vhwdeveloper']?'Hardware devel, ':'');
    $rv.= ($v['reg_vmediapro']?'Media Professional, ':'');
    $rv.= ($v['reg_vmproducer']?'Music Producer, ':'');
    $rv.= ($v['reg_vvproducer']?'Video Producer, ':'');
    $rv.= ($v['reg_vresearcher']?'Researcher, ':'');
    $rv.= ($v['reg_vswuser']?'User, ':'');
    $rv.= ($v['reg_vpress']?'Press, ':'');
    $rv.= ($v['reg_vinterested']?'Just Interested, ':'');
    $rv.= ($v['reg_vother']?'Other':'');
    $rv.= '"'.$sep;
    $rv.= '"'.$v['reg_profession'].'"'.$sep;
    $rv.= '"'.($v['reg_proceedings']?'yes':'no').'"'.$sep;
		$rv.= '"'.($v['reg_whoelselist']?'yes':'no').'"'.$sep;
    if (isset($v['reg_vip'])) {
			$rv.= '"'.$v['reg_vip'].'"'.$sep;
		} else {
			$rv.= '""'.$sep;
		}
    $rv.= '"'.$v['reg_notes'].'"'."\n";
  }
  return $rv;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
function gen_badges_pdf($f) {
  $handle = fopen(TMPDIR.'/lac2012badges.tex', "w");
  fwrite($handle, gen_badges_source($f));
  fclose($handle);
  @copy (TMPDIR.'../img/badge_ccrma.png', TMPDIR.'/badge_ccrma.png');
  @copy (TMPDIR.'../img/badgelogo.png', TMPDIR.'/badgelogo.png'); # XXX FIX img path
  @copy (TMPDIR.'../img/fonts/GoudyStMTT.afm', TMPDIR.'/GoudyStMTT.afm'); 
  @copy (TMPDIR.'../img/fonts/GoudyStMTT.tfm', TMPDIR.'/GoudyStMTT.tfm'); 
  @copy (TMPDIR.'../img/fonts/GoudyStMTT.ttf', TMPDIR.'/GoudyStMTT.ttf'); 
  @copy (TMPDIR.'../img/fonts/ttfonts.map', TMPDIR.'/ttfonts.map'); 
  @copy (TMPDIR.'../img/fonts/T1-WGL4x.enc', TMPDIR.'/T1-WGL4x.enc'); 

  @unlink (TMPDIR.'/lac2012badges.pdf');
  echo '<pre style="font-size:70%; line-height:1.2em;">';
  system('cd '.TMPDIR.'; pdflatex lac2012badges.tex');
  echo '</pre>';
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////

function mynamesort($a,$b) {
  $a = preg_replace('@^[0-9_]*-@', '', $a);
  $b = preg_replace('@^[0-9_]*-@', '', $b);
  return strcasecmp($a, $b);
}

function mytimesort($a,$b) {
  return strcasecmp($a, $b);
}


function gen_badges_source($f) {
  usort($f, 'mytimesort');
  $cnt=0;
  $rv=badge_tex_header();
  $rv.='%
\begin{picture}(7.5,10.5)%
\cuts
';
  foreach ($f as $fn) {
    if (false) { // skip already printed registrations XXX
      $regtime=preg_replace('@-.*$@', '', $fn);
      if (strcasecmp($regtime, '20100426_193156') <= 0) continue;
    }

    $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
    $v=parse_ini_file(REGLOGDIR.$filename);
    $name=str_replace(',','',$v['reg_prename'].' '.$v['reg_name']);
    $name=texify_umlauts($name);
    $what=texify_umlauts($v['reg_tagline']);
    $badgebg='';
    if (isset($v['reg_vip'])) {
      switch(strtolower($v['reg_vip'])) {
        case 'author':
          $badgebg='AUTHOR';
          break;
        case 'organizer':
          $badgebg='ORGANIZER';
          break;
        default:
          $badgebg='';
          break;
      }
    }

# http://web.image.ufl.edu/help/latex/fonts.shtml
#\tiny 5 5
#\scriptsize 7 7
#\footnotesize 8 8
#\small 9 9
#\normalsize 10 10
#\large 12 12
#\Large 14 14.40
#\LARGE 18 17.28
#\huge 20 20.74
#\Huge 24 24.88
  if (0) {
    # DEFAULT FONT:
    if (strlen($name) > 40) $name='\normalsize '.$name; # TODO verify size!
    elseif (strlen($name) > 26) $name='\large '.$name; 
    elseif (strlen($name) > 20) $name='\LARGE '.$name;
    else  $name='\Huge '.$name;

    if (strlen($what) > 56) $what='\tiny '.$what; 
    elseif (strlen($what) > 49) $what='\scriptsize '.$what;
    elseif (strlen($what) > 40) $what='\footnotesize '.$what; 
		else $what='\normalsize '.$what; 

    if (!empty($badgebg)) $badgebg='\small '.$badgebg;

  } else {

    # Goudy FONT:
    if (strlen($name) > 40) $name='\GoudyStMTT '.$name; # TODO verify size!
    elseif (strlen($name) > 26) $name='\GoudyStMTTlarge '.$name; 
    elseif (strlen($name) > 20) $name='\GoudyStMTTLARGE '.$name;
    else  $name='\GoudyStMTTHuge '.$name;

    if (strlen($what) > 56) $what='\GoudyStMTTtiny '.$what; 
    elseif (strlen($what) > 49) $what='\GoudyStMTTscriptsize '.$what;
    elseif (strlen($what) > 40) $what='\GoudyStMTTfootnotesize '.$what; 
    else $what='\GoudyStMTT '.$what; 

    if (!empty($badgebg)) $badgebg='\GoudyStMTTsmall '.$badgebg;

  }

    #$x=($cnt%2)?"0.0":"3.4";
    #$y=8-(($cnt%5)*2);
    $x=($cnt%2)?"3.4":"0.0";
    $y=8-2*floor(($cnt%10)/2);

    $y+=0.1; ## vertical offset

    $rv.='\put('.$x.','.$y.'){\makebox(3.5,2.0){\card{'.$name.'}{'.$what.'}{'.$badgebg.'}}}'."\n";
    $cnt++;
    if ($cnt%10 == 0) {
      $rv.='%
\end{picture}

\pagebreak

\begin{picture}(7.5,10.5)%
\cuts
';
    }
  }
$rv.='%
\end{picture}

\end{document}
';
 return $rv;
}

function badge_tex_header() {
  return '
\documentclass{article}
\usepackage{a4}

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% FONTS %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
\usepackage{fontenc}                                                            
\font\GoudyStMTTtiny GoudyStMTT at 6pt
\font\GoudyStMTTscriptsize GoudyStMTT at 7pt
\font\GoudyStMTTfootnotesize GoudyStMTT at 8pt
\font\GoudyStMTTsmall GoudyStMTT at 9pt
\font\GoudyStMTT GoudyStMTT at 10pt
\font\GoudyStMTTlarge GoudyStMTT at 14pt
\font\GoudyStMTTLARGE GoudyStMTT at 18pt
\font\GoudyStMTThuge GoudyStMTT at 20pt
\font\GoudyStMTTHuge GoudyStMTT at 24pt

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% MARGINS %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
\textwidth       7.50in
\textheight     10.50in
\oddsidemargin   -.25in
\evensidemargin  -.25in
\topmargin      -1.50in
\itemindent      0.00in
\parindent       0.00in

%%%%%%%%%%%%%%%%%%%%%%% IMAGES FOR LATEX AND PDFLATEX %%%%%%%%%%%%%%%%%%%%%%%
\ifnum \pdfoutput=0
  \usepackage[dvips]{graphicx}
  \usepackage{epsfig}
\else
  \usepackage[pdftex]{graphicx}
\fi
\newcommand{\image}[2]{
  \ifnum \pdfoutput=0
    \includegraphics[#1]{#2.eps}
  \else
    \includegraphics[#1]{#2.png}
  \fi
}

%%%%%%%%%%%%%%%%%%%%%%%%%%%%% CARD MACRO [\card] %%%%%%%%%%%%%%%%%%%%%%%%%%%%
\def\card#1#2#3{
        \parbox[c][4.5cm]{9.8cm}{
        \vspace*{1.9cm}
        \hspace*{3.5cm}
        \image{height=3.5cm,width=3.5cm}{badge_ccrma}
        }
        \hspace*{-9.8cm}
        \begin{tabular}{c}
	\hspace*{.20in}\image{height=1.2cm,width=5.28cm}{badgelogo}
	\rule[0.80ex]{0.70in}{.5pt}\\\\%
	%\hspace*{0.70in}\\\\%
	\small%
	\begin{tabular}[b]{lcr}%
	%\hspace*{.25in}\small LAC 2012 & \hspace*{1.15in} & \hspace*{0.15in}CCRMA Stanford\\\\%
	\hspace*{.25in}\GoudyStMTT LAC 2012 & \hspace*{1.15in} & \hspace*{0.15in}\GoudyStMTT CCRMA Stanford\\\\%
	\end{tabular}\\\\%
	\vspace{0.05in}\\\\%
	\hspace*{.25in}{#1}\\\\%
	\vspace*{-0.12in}\\\\%
	\hspace*{.25in}{#3}\\\\%
	\vspace*{-0.12in}\\\\%
        \hspace*{.25in}{#2}\\\\%
	\end{tabular}%
}

%%%%%%%%%%%%%%%%%%%%%%%%%%%%% CUT MARKS [\cuts] %%% %%%%%%%%%%%%%%%%%%%%%%%%%
\def\cuts{
	\put(-0.1,10.0){\rule{0.2cm}{0.5pt}}\\\\%
	\put(-0.1,8.0){\rule{0.2cm}{0.5pt}}\\\\%
	\put(-0.1,6.0){\rule{0.2cm}{0.5pt}}\\\\%
	\put(-0.1,4.0){\rule{0.2cm}{0.5pt}}\\\\%
	\put(-0.1,2.0){\rule{0.2cm}{0.5pt}}\\\\%
	\put(-0.1,0.0){\rule{0.2cm}{0.5pt}}\\\\%
	\put(7.1,10.0){\rule{0.2cm}{0.5pt}}\\\\%
	\put(07.1,8.0){\rule{0.2cm}{0.5pt}}\\\\%
	\put(07.1,6.0){\rule{0.2cm}{0.5pt}}\\\\%
	\put(07.1,4.0){\rule{0.2cm}{0.5pt}}\\\\%
	\put(07.1,2.0){\rule{0.2cm}{0.5pt}}\\\\%
	\put(07.1,0.0){\rule{0.2cm}{0.5pt}}\\\\%
%
	\put(0.2,-0.2){\line(0,1){0.1}}%
	\put(3.6,-0.2){\line(0,1){0.1}}%
	\put(7.0,-0.2){\line(0,1){0.1}}%
	\put(0.2,10.1){\line(0,1){0.1}}%
	\put(3.6,10.1){\line(0,1){0.1}}%
	\put(7.0,10.1){\line(0,1){0.1}}%
}

%%%%%%%%%%%%%%%%%%%%%%%%%%%% BEGIN DOCUMENT %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
\pagestyle{empty}
\begin{document}
\setlength{\unitlength}{1in}%
';
}
