<?php
session_start();
if(empty($_SESSION['opt'])) die();

$toparse = unserialize($_SESSION['opt']);

require_once("libhorario.php");
set_time_limit(500);
ignore_user_abort(true);
//$session['opt'] = "";
$ii = new IDHorario($toparse['plan'], $toparse['centro'], $toparse['curso'], $toparse['grupo'], $toparse['cuatri']);
//echo('<meta HTTP-EQUIV="REFRESH" content="0; url='.$ii->getFolder().'">');

// parseHorario will redirect to the final page and then keep generating the timetable on the background.
// The final page will server-side poll for `status.dat` and _reload itself_ until present.
parseHorario($ii);

?>
