<?php
if (!defined('REGLOGDIR')) die();
?>
<form action="index.php" method="post" name="myform">
<?php
  $mode='';
  if (isset($_REQUEST['mode']))
    $mode=rawurldecode($_REQUEST['mode']);

  $showdefault=false;

  switch ($mode) {
    case 'unlocklocation':
      $id=intval(rawurldecode($_REQUEST['param'])); 
      unlock($db, $id, 'location');
    case 'listlocation':
      admin_fieldset();
      program_fieldset();
      echo '<legend>Location List:</legend>'."\n";
      dbadmin_listlocations($db);
      break;
    case 'unlockuser':
      $id=intval(rawurldecode($_REQUEST['param'])); 
      unlock($db, $id, 'user');
    case 'listuser':
      admin_fieldset();
      program_fieldset();
      echo '<legend>Author List:</legend>'."\n";
      dbadmin_listusers($db);
      break;
    case 'editlocation':
      $id=intval(rawurldecode($_REQUEST['param'])); 
      if (lock($db, $id, 'location') === -1 ) {
        program_fieldset();
        echo '<legend>Location Entry:</legend>'."\n";
        dbadmin_locationform($db, $id); 
      } else {
        echo '<div class="dbmsg">Entry is currently being edited.</div>'."\n";
        $showdefault=true;
      }
      break;
    case 'edituser':
      $id=intval(rawurldecode($_REQUEST['param'])); 
      if (lock($db, $id, 'user') === -1 ) {
        program_fieldset();
        echo '<legend>Author Entry:</legend>'."\n";
        dbadmin_authorform($db, $id); 
      } else {
        echo '<div class="dbmsg">Entry is currently being edited.</div>'."\n";
        $showdefault=true;
      }
      break;
    case 'edit':
      $id=intval(rawurldecode($_REQUEST['param'])); 
      if (lock($db, $id) === -1 ) {
        program_fieldset();
        echo '<legend>Program Entry:</legend>'."\n";
        dbadmin_editform($db, $id); 
      } else {
        echo '<div class="dbmsg">Entry is currently being edited.</div>'."\n";
        $showdefault=true;
      }
      break;
    case 'export':
			$handle = fopen(TMPDIR.'/schedule.csv', "w");
			fwrite($handle, export_progam_sv($db, ","));
			fclose($handle);
			echo 'Download: <a href="download.php?file=schedule.csv">schedule.csv</a>';
      break;
    case 'orphans':
      admin_fieldset();
      program_fieldset();
      echo '<legend>Conference Program - Orphan entries:</legend>'."\n";
      dbadmin_orphans($db);
      echo '</fieldset>';
      break;
    case 'conflicts':
      admin_fieldset();
      program_fieldset();
      echo '<legend>Conference Program - Schedule conflicts:</legend>'."\n";
      dbadmin_checkconflicts($db);
      echo '</fieldset>';
      break;
    case 'unlockactivity':
      $id=intval(rawurldecode($_REQUEST['param'])); 
      unlock($db, $id);
    case 'delentry': # TODO :check if locked before deleting..
      if ($mode==='delentry') dbadmin_delentry($db);
    case 'deluser': # TODO :check if locked before deleting..
      if ($mode==='deluser') dbadmin_deluser($db);
    case 'dellocation': # TODO :check if locked before deleting..
      if ($mode==='dellocation') dbadmin_dellocation($db);
    case 'savelocation':
      if ($mode==='savelocation') dbadmin_savelocation($db);
    case 'saveuser':
      if ($mode==='saveuser') dbadmin_saveuser($db);
    case 'saveedit':
      if ($mode==='saveedit') dbadmin_saveedit($db);
    default:
      $showdefault=true;
      break;
  }

  if ($showdefault) {
    admin_fieldset();
    program_fieldset();
    echo '<legend>Conference Program:</legend>'."\n";
    $sort=''; if (isset($_REQUEST['sort'])) $sort=rawurldecode($_REQUEST['sort']);
    dbadmin_listall($db, $sort);
  }

?>
  </fieldset>
</form>
