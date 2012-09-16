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
    $handle = fopen(TMPDIR.'registrations.csv', "w");
    fwrite($handle, export_sv(","));
    fclose($handle);
    echo 'Download: <a href="download.php?file=registrations.csv">registrations.csv</a>';
    break;
  case 'email':
    $r=scan_registrations();
    echo 'Email copy/paste:<br/>';
    echo '<pre style="font-size:9px; background:#ccc; line-height:1.3em;">'."\n";
    echo wordwrap(list_emails($r),100)."\n";
    echo '</pre><br/>'."\n"; 
    echo 'List of Participants:<br/>';
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
      set_vip(rawurldecode($_POST['param']), $vip);
  case 'list':
    $r=scan_registrations();
    echo '<p>We have '.count($r).' registered participants:</p>';
    echo '<table class="adminlist" cellspacing="0">'."\n";
    echo '<tr><th>Name</th><th></th><th colspan="3">Change Attribution</th></tr>';
    foreach ($r as $f) {
      echo '<tr><td style="border-bottom: dotted 1px;">';
      echo substr($f, 16);
      echo '</td><td>';
      echo '<span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'detail\';formsubmit(\'myform\');">Show Details</span>';
      echo '</td><td>';

      $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $f).'.ini';
      $v=parse_ini_file(REGLOGDIR.$filename);

      if (!isset($v['reg_vip'])) { $v['reg_vip']=''; }
      switch(strtolower($v['reg_vip'])) {
        case 'author':
          echo '<td><span style="font-weight:bold;">[Author]</span></td>';
          echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_organizer\';formsubmit(\'myform\');">Organizer</span></td>';
          echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_none\';formsubmit(\'myform\');">No-VIP</span></td>';
          break;
        case 'organizer':
          echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_author\';formsubmit(\'myform\');">Author</span></td>';
          echo '<td><span style="font-weight:bold;">[Organizer]</span></td>';
          echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_none\';formsubmit(\'myform\');">No-VIP</span></td>';
          break;
        default:
          echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_author\';formsubmit(\'myform\');">Author</span></td>';
          echo '<td><span style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'vip_organizer\';formsubmit(\'myform\');">Organizer</span></td>';
          echo '<td><span>[No-VIP]</span></td>';
          break;
      }
      echo '</td></tr>'."\n";
    }
    echo '</table>';
    break;
  default:
    echo 'Choose an action from the menu.';
    break;
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
  $found=0;
  echo "<ul>\n";
  foreach ($f as $fn) {
    $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
    $v=parse_ini_file(REGLOGDIR.$filename);
    if (!empty($v[$k])) {
      $found++;
      echo '<li style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($fn).'\';document.getElementById(\'mode\').value=\'detail\';formsubmit(\'myform\');">';
      echo $v['reg_prename'].' '.$v['reg_name'].': '.$v[$k].'</li>'."\n";
    }
  }
  echo "</ul>\n";
  if ($found==0 ) {
    echo '<div class="error">No entries found.</div>';
    return;
  }
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

#export SV escape
function exes($text, $sep="\t") {
  # replace $sep with /space/; replace '"' with "''"
  if ($sep != ' ') 
    $text=str_replace($sep,' ',$text);
  $text=str_replace('"',"''",$text);
  return $text;
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

    $rv.= '"'.exes($v['reg_name']).'"'.$sep;
    $rv.= '"'.exes($v['reg_prename']).'"'.$sep;
    $rv.= '"'.exes($v['reg_tagline']).'"'.$sep;
    $rv.= '"'.exes($v['reg_email']).'"'.$sep;
    $rv.= '"'.exes(preg_replace('/A(\d{2})(.{2})/','${1}-${2}',$v['reg_agegroup'])).'"'.$sep;
    $rv.= '"'.exes($v['reg_country']).'"'.$sep;
    $rv.= '"'.($v['reg_useathome']?'at home, ':'').($v['reg_useatwork']?'at work':'').'"'.$sep;
    $rv.= '"'
      .(($v['reg_audiopro']==1)?'no':'')
      .(($v['reg_audiopro']==2)?'yes':'')
      .(($v['reg_audiopro']==0)?'??':'');
    $rv.= '"'.$sep;
    $rv.= '"'.exes($v['reg_about']);
#   $rv.= '"';
#   $rv.= ($v['reg_vmusician']?'Composer or musician, ':'');
#   $rv.= ($v['reg_vdj']?'DJ, ':'');
#   $rv.= ($v['reg_vswdeveloper']?'Software devel, ':'');
#   $rv.= ($v['reg_vhwdeveloper']?'Hardware devel, ':'');
#   $rv.= ($v['reg_vmediapro']?'Media Professional, ':'');
#   $rv.= ($v['reg_vmproducer']?'Music Producer, ':'');
#   $rv.= ($v['reg_vvproducer']?'Video Producer, ':'');
#   $rv.= ($v['reg_vresearcher']?'Researcher, ':'');
#   $rv.= ($v['reg_vswuser']?'User, ':'');
#   $rv.= ($v['reg_vpress']?'Press, ':'');
#   $rv.= ($v['reg_vinterested']?'Just Interested, ':'');
#   $rv.= ($v['reg_vother']?'Other':'');
    $rv.= '"'.$sep;
    $rv.= '"'.$v['reg_profession'].'"'.$sep;
    $rv.= '"'.($v['reg_proceedings']?'yes':'no').'"'.$sep;
    $rv.= '"'.($v['reg_whoelselist']?'yes':'no').'"'.$sep;
    if (isset($v['reg_vip'])) {
      $rv.= '"'.exes($v['reg_vip']).'"'.$sep;
    } else {
      $rv.= '""'.$sep;
    }
    $rv.= '"'.exes($v['reg_notes']).'"'."\n";
  }
  return $rv;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
function gen_badges_pdf($f) {
  $handle = fopen(TMPDIR.'lac2012badges.tex', "w");
  fwrite($handle, gen_badges_source($f));
  fclose($handle);
  @copy (DOCROOTDIR.'img/badge_iem.png', TMPDIR.'badge_iem.png');
  @copy (DOCROOTDIR.'img/badgelogo.png', TMPDIR.'badgelogo.png');
  @copy (DOCROOTDIR.'img/fonts/VeraMoBd.afm', TMPDIR.'VeraMoBd.afm'); 
  @copy (DOCROOTDIR.'img/fonts/VeraMoBd.tfm', TMPDIR.'VeraMoBd.tfm'); 
  @copy (DOCROOTDIR.'img/fonts/VeraMoBd.ttf', TMPDIR.'VeraMoBd.ttf'); 
  @copy (DOCROOTDIR.'img/fonts/ttfonts.map', TMPDIR.'ttfonts.map'); 
  @copy (DOCROOTDIR.'img/fonts/T1-WGL4x.enc', TMPDIR.'T1-WGL4x.enc'); 
  @copy (DOCROOTDIR.'img/badgeback.pdf', TMPDIR.'badgeback.pdf');

  @unlink (TMPDIR.'lac2012badges.pdf');
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
\begin{picture}(7.5,10.6)%
\cuts
';
  foreach ($f as $fn) {
    if (true) { // skip already printed registrations XXX
      $regtime=preg_replace('@-.*$@', '', $fn);
      if (strcasecmp($regtime, '20120411_184843') <= 0) continue;
    }

    $filename=$name = preg_replace('/[^a-zA-Z0-9_-]/','_', $fn).'.ini';
    $v=parse_ini_file(REGLOGDIR.$filename);
    $firstname=str_replace(',','',$v['reg_prename']);
    $name=str_replace(',','', $v['reg_name']);
    $firstname=texify_umlauts(trim($firstname));
    $name=texify_umlauts(trim($name));
    $what=texify_umlauts(trim($v['reg_tagline']));
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
    if (strlen($name) > 40)     $name='\large '.$name; # TODO verify size!
    elseif (strlen($name) > 26) $name='\LARGE '.$name; 
    elseif (strlen($name) > 20) $name='\huge '.$name;
    else                        $name='\sffamily\fontsize{48}{56}\selectfont '.$name;

    if (strlen($what) > 56)     $what='\small '.$what; 
    elseif (strlen($what) > 49) $what='\normalsize '.$what;
    elseif (strlen($what) > 40) $what='\large '.$what; 
    else                        $what='\Large '.$what; 

    if (!empty($badgebg)) $badgebg='\normalsize '.$badgebg;

  } else {

    # Vera/Goudy FONT:
    if (strlen($firstname) > 40) $firstname='\GoudyStMTT '.$firstname; # TODO verify size!
    elseif (strlen($firstname) > 26) $firstname='\GoudyStMTTlarge '.$firstname; 
    elseif (strlen($firstname) > 16) $firstname='\GoudyStMTTLARGE '.$firstname;
		else  $firstname='\GoudyStMTTHuge '.$firstname;

    if (strlen($name) > 40) $name='\GoudyStMTT '.$name; # TODO verify size!
    elseif (strlen($name) > 26) $name='\GoudyStMTTlarge '.$name; 
    elseif (strlen($name) > 16) $name='\GoudyStMTTLARGE '.$name;
    else  $name='\GoudyStMTTHuge '.$name;

		if (empty($what))           $what='\large\vspace{1ex}';
		else if (strlen($what) > 56)$what='\normalsize '.$what; 
    elseif (strlen($what) > 49) $what='\large '.$what;
    elseif (strlen($what) > 40) $what='\Large '.$what; 
    else                        $what='\Large '.$what; 

		if (!empty($badgebg)) $badgebg='\normalsize '.$badgebg;
		else $badgebg='\normalsize\vspace{1ex}';

  }

    $x=($cnt%2)?"3.6":"0.0";
    $y=5-4.5*floor(($cnt%4)/2);

    $y+=0.1; ## vertical offset

    $rv.='\put('.$x.','.$y.'){\makebox(4.5,3.6){\card{'.$firstname.'}{'.$name.'}{'.$what.'}{'.$badgebg.'}}}'."\n";
    $cnt++;
    if ($cnt%4 == 0) {
      $rv.='%
\end{picture}

\pagebreak

\begin{picture}(7.5,10.5)%
\cuts
\put(0.0,5.1){\rotatebox{0}{\includegraphics{badgeback.pdf}}}
\put(3.6,5.1){\rotatebox{0}{\includegraphics{badgeback.pdf}}}
\put(0.0,0.6){\rotatebox{0}{\includegraphics{badgeback.pdf}}}
\put(3.6,0.6){\rotatebox{0}{\includegraphics{badgeback.pdf}}}
\end{picture}

\pagebreak

\begin{picture}(7.5,10.6)%
\cuts
';
    }
  }
$rv.='%
\end{picture}

\pagebreak

\begin{picture}(7.5,10.5)%
\cuts
\put(0.0,4.4){\rotatebox{0}{\includegraphics{badgeback.pdf}}}
\put(3.6,4.4){\rotatebox{0}{\includegraphics{badgeback.pdf}}}
\put(0.0,-0.1){\rotatebox{0}{\includegraphics{badgeback.pdf}}}
\put(3.6,-0.1){\rotatebox{0}{\includegraphics{badgeback.pdf}}}
\end{picture}

\end{document}
';
 return $rv;
}

# The size of each card is 86 x 51 mm.
# Standard badge-holder size: 90 x 56mm. -- 2.25 x 3.5 inch 
function badge_tex_header() {
  return '
\documentclass{article}
%\usepackage{a4}
\usepackage[T1]{fontenc}
\font\GoudyStMTTtiny fonts/VeraMoBd at8pt
\font\GoudyStMTTscriptsize fonts/VeraMoBd at9pt
\font\GoudyStMTTfootnotesize fonts/VeraMoBd at10pt
\font\GoudyStMTTsmall fonts/VeraMoBd at12pt
\font\GoudyStMTT fonts/VeraMoBd at14pt
\font\GoudyStMTTlarge fonts/VeraMoBd at20pt
\font\GoudyStMTTLARGE fonts/VeraMoBd at26pt
\font\GoudyStMTTHuge fonts/VeraMoBd at34pt

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% MARGINS %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
\textwidth       7.50in
\textheight     10.60in
\oddsidemargin   -.35in
\evensidemargin  -.35in
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
\def\card#1#2#3#4{
	\rotatebox{-90}{
	\parbox[c][3.3in]{4.0in}{
  \vspace*{3.5in}
  \hspace*{-0.25in}
  \image{height=1cm,width=4.84cm}{badge_iem}
  }
  \hspace*{-4.4in}
  \begin{tabular}{c}
{ 
  \vspace*{1.0in}
	\hspace*{-.70in}\image{height=1.25cm,width=5.5cm}{badgelogo}
  \parbox[c]{1in}{\vspace*{-0.30in}Conference\\\\2012}}\\\\%
  \vspace{-1.00in}\\\\%
  \hspace*{-.70in}{#1}\\\\%
  \hspace*{-.70in}{#2}\\\\%
  \vspace*{0.5ex}\\\\%
  \hspace*{-.67in}{#4}\\\\%
  \vspace*{-0.5ex}\\\\%
  \hspace*{-.70in}{#3}\\\\%
		\end{tabular}%
}
}

%%%%%%%%%%%%%%%%%%%%%%%%%%%%% CUT MARKS [\cuts] %%% %%%%%%%%%%%%%%%%%%%%%%%%%
\def\cuts{
  \put(-0.2,9.55){\rule{0.2cm}{0.5pt}}\\\\%
  \put(-0.2,5.1){\rule{0.2cm}{0.5pt}}\\\\%
	\put(-0.2,0.65){\rule{0.2cm}{0.5pt}}\\\\%

  \put(7.3,9.55){\rule{0.2cm}{0.5pt}}\\\\%
  \put(7.3,5.1){\rule{0.2cm}{0.5pt}}\\\\%
	\put(7.3,0.65){\rule{0.2cm}{0.5pt}}\\\\%

	\put(0.0,0.4){\line(0,1){0.1}}%
	\put(3.6,0.4){\line(0,1){0.1}}%
	\put(7.2,0.4){\line(0,1){0.1}}%

	\put(0.0,9.7){\line(0,1){0.1}}%
	\put(3.6,9.7){\line(0,1){0.1}}%
	\put(7.2,9.7){\line(0,1){0.1}}%
}

%%%%%%%%%%%%%%%%%%%%%%%%%%%% BEGIN DOCUMENT %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
\pagestyle{empty}
\RequirePackage{fix-cm}
\begin{document}
\setlength{\unitlength}{1in}%
';
}
