<?php

if (!defined('REGLOGDIR')) die();

$mode='';
if (isset($_POST['mode'])) $mode=rawurldecode($_POST['mode']);

adminpage();
switch ($mode) {
  case 'csv':
    $handle = fopen(TMPDIR.'/registrations.csv', "w");
    fwrite($handle, export_sv(","));
    fclose($handle);
    echo 'Download: <a href="download.php?secret='.$_REQUEST['secret'].'&amp;file=registrations.csv">registrations.csv</a>';
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
    echo 'Download: <a href="download.php?secret='.$_REQUEST['secret'].'&amp;file=lac2011badges.pdf">lac2011badges.pdf</a>';
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
    echo '<p>Got '.$v.' requests for proceesings out of '.count($r).' total registrations.</p>';
    show_fields($r,'reg_proceedings');
    break;
  case 'food':
    $r=scan_registrations();
    show_fields($r,'reg_food');
    break;
  case 'detail':
    show_registration($_POST['param']);
    break;
  case 'list':
    $r=scan_registrations();
    echo '<p>We have '.count($r).' registered participants:</p>';
    echo '<ul>';
    foreach ($r as $f) {
      echo '<li style="cursor:pointer; color:blue;" onclick="document.getElementById(\'param\').value=\''.rawurlencode($f).'\';document.getElementById(\'mode\').value=\'detail\';document.myform.submit();">';
      echo $f.'</li>'."\n";
    }
    echo '</ul>';
    break;
  default:
    break;
}

function adminpage() {
  echo '
<form action="index.php" method="post" name="myform">
  <fieldset class="fm">
    <input name="page" type="hidden" value="admin" id="page"/>
    <input name="mode" type="hidden" value="" id="mode"/>
    <input name="param" type="hidden" value="" id="param"/>
    <input name="secret" type="hidden" value="'.$_REQUEST['secret'].'"/>
    <legend>Registration Admin:</legend>
    <input class="button" type="button" title="List all registrations" value="List Participants" onclick="document.getElementById(\'mode\').value=\'list\';document.myform.submit();"/>
    <input class="button" type="button" title="Show non empty food requests" value="List Food Requests" onclick="document.getElementById(\'mode\').value=\'food\';document.myform.submit();"/>
    <input class="button" type="button" title="Show non empty remarks" value="List Remarks" onclick="document.getElementById(\'mode\').value=\'remarks\';document.myform.submit();"/>
    <br/>
    <input class="button" type="button" title="Generate list of email addresses" value="Dump Email Contacts" onclick="document.getElementById(\'mode\').value=\'email\';document.myform.submit();"/>
    <input class="button" type="button" title="Count Ordered Proceedings" value="Count Ordered Proceedings" onclick="document.getElementById(\'mode\').value=\'proceedings\';document.myform.submit();"/>
    <br/>
<!-- <input class="button" type="button" title="Show Badges TeX" value="Show Badges TeX" onclick="document.getElementById(\'mode\').value=\'badgestex\';document.myform.submit();"/> !-->
    <input class="button" type="button" title="Generate badges PDF" value="Generate Badges PDF" onclick="document.getElementById(\'mode\').value=\'badgespdf\';document.myform.submit();"/>
    <input class="button" type="button" title="Export comma separated value table" value="Export CSV" onclick="document.getElementById(\'mode\').value=\'csv\';document.myform.submit();"/>
    <br/>
  </fieldset>
  <fieldset class="fm">
    <legend>Program Admin:</legend>
    <input class="button" type="button" title="List Program Entries" value="List Program Entries" onclick="document.getElementById(\'page\').value=\'adminschedule\';document.myform.submit();"/>
    <input class="button" type="button" title="List Authors" value="List Authors" onclick="document.getElementById(\'mode\').value=\'listuser\';document.getElementById(\'page\').value=\'adminschedule\';document.myform.submit();"/>&nbsp;
    <input class="button" type="button" title="List Locations" value="List Locations" onclick="document.getElementById(\'mode\').value=\'listlocation\';document.getElementById(\'page\').value=\'adminschedule\';document.myform.submit();"/>&nbsp;
  </fieldset>
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
  $rv.= '"City"'.$sep;
  $rv.= '"County"'.$sep;
  $rv.= '"Using Linux"'.$sep;
  $rv.= '"Profi"'.$sep;
  $rv.= '"Interests"'.$sep;
  $rv.= '"Profession"'.$sep;
  $rv.= '"Proceedings"'.$sep;
  $rv.= '"VIP"'.$sep;
  $rv.= '"Food"'.$sep;
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
    $rv.= '"'.$v['reg_city'].'"'.$sep;
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
    $rv.= '"'.$v['reg_vip'].'"'.$sep;
    $rv.= '"'.$v['reg_food'].'"'.$sep;
    $rv.= '"'.$v['reg_notes'].'"'."\n";
  }
  return $rv;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
function gen_badges_pdf($f) {
  $handle = fopen(TMPDIR.'/lac2011badges.tex', "w");
  fwrite($handle, gen_badges_source($f));
  fclose($handle);
  @copy (TMPDIR.'../img/badge_nuim.png', TMPDIR.'/badge_nuim.png');
  @copy (TMPDIR.'../img/badgelogo.png', TMPDIR.'/badgelogo.png'); # XXX FIX img path
  @unlink (TMPDIR.'/lac2011badges.pdf');
  echo '<pre style="font-size:70%; line-height:1.2em;">';
  system('cd '.TMPDIR.'; pdflatex lac2011badges.tex');
  echo '</pre>';
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
function texify_umlauts($v) {
  $v=str_replace("\xc3\x9f",'\\"{s}',$v);
  $v=str_replace("\xc3\xa0",'\\`{a}',$v);
  $v=str_replace("\xc3\xa1",'\\\'{a}',$v);
  $v=str_replace("\xc3\xa2",'\\\^{a}',$v);
  $v=str_replace("\xc3\xa4",'\\"{a}',$v);
  $v=str_replace("\xc3\xa8",'\\`{e}',$v);
  $v=str_replace("\xc3\xa9",'\\\'{e}',$v);
  $v=str_replace("\xc3\xaa",'\\^{e}',$v);
  $v=str_replace("\xc3\xb6",'\\"{o}',$v);
  $v=str_replace("\xc3\xb9",'\\`{u}',$v);
  $v=str_replace("\xc3\xba",'\\\'{u}',$v);
  $v=str_replace("\xc3\xbc",'\\"{u}',$v);
  $v=str_replace("\xc3\xbd",'\\\'{y}',$v);
  $v=str_replace("\xc3\xbf",'\\"{y}',$v);
  return $v;
}

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

    if (strlen($name) > 40) $name='\normalsize '.$name; # TODO verify size!
    elseif (strlen($name) > 26) $name='\large '.$name; 
    elseif (strlen($name) > 20) $name='\LARGE '.$name;

    if (strlen($what) > 56) $what='\tiny '.$what; 
    elseif (strlen($what) > 49) $what='\scriptsize '.$what;
    elseif (strlen($what) > 40) $what='\footnotesize '.$what; 

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
        \parbox[c][4.5cm]{8.8cm}{
        \vspace*{1.8cm}
        \hspace*{1.5cm}
	\image{height=2.62cm,width=6.0cm}{badge_nuim}
        }
        \hspace*{-8.8cm}
        \begin{tabular}{c}
	\hspace*{.20in}\image{height=1.2cm,width=5.28cm}{badgelogo}
	\rule[0.80ex]{0.70in}{.5pt}\\\\%
	\small%
	\begin{tabular}[b]{lcr}%
	\hspace*{.25in}\small LAC 2011 & \hspace*{1.15in} & \hspace*{0.15in}\\\\%
	\end{tabular}\\\\%
	\vspace{0.05in}\\\\%
	\hspace*{.25in}{\Huge #1}\\\\%
	\vspace*{-0.12in}\\\\%
	\hspace*{.25in}{\small #3}\\\\%
	\vspace*{-0.12in}\\\\%
        \hspace*{.25in}{\normalsize #2}\\\\%
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
