<?php
session_start();
require_once("tools.php");
require_once("libhorario.php");
//
$fields = array("plan","centro","grupo","curso","cuatri");
foreach($fields as $field) {
	$f = $_POST[$field];
	if(!is_numeric($f)) {
		header("Location: index.php");
		die();
	}

	$serfields[$field] = $f;
}
$tree = unserialize(file_get_contents("data/tree.dat"));
if(!in_array($_POST['cuatri'], $tree[$_POST['plan']][$_POST['centro']][$_POST['curso']][$_POST["grupo"]])) {
	header("Location: index.php");
	die();
}
$_SESSION['opt'] = serialize($serfields);
$ii = new IDHorario($_POST['plan'], $_POST['centro'], $_POST['curso'], $_POST['grupo'], $_POST['cuatri']);
$url = $ii->getURL();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Horarios UC3M</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="/stylesheets/foundation.css" type="text/css" />
	<link rel="stylesheet" href="/stylesheets/app.css" type="text/css" />

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js"></script>
	<script>
		$(function() {
		$('iframe#loadFrame').attr('src', "<?php echo $url; ?>");
		$('iframe#loadFrame').load(function() 
		{
			$('#envi').removeAttr("disabled")
			$('.done').show();
			$('#wait').hide();
		});
	});
	</script>
</head>

<body>
	<div class="row">
	<div class="twelve columns">
					<h2>Horarios UC3M</h2>
					<hr />
				</div>
	<form action="gen.php" method="post" class="nice">
	<span class="done" style="display: none"><h5>Este es el horario que se convertirá a formato iCalendar. Comprueba que sea el correcto, y pulsa Continuar.</h5></span>
	<span id="wait"><h5>Esperando a que la web de la UC3M genere el horario. Espera... (ten paciencia, a veces le cuesta un poco)</h5></span>
	<div class="row"><iframe id="loadFrame" width="99%" height="500px" class="twelve columns"> </iframe></div>
	<div class="row"><div class="two columns centered done" style="display: none"><input type="submit" id="envi" disabled value="Continuar ->" class="button"/></div></div>
	</div>
	
	</form>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-28082896-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>

</html>
