<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>
			Horarios UC3M
		</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="generator" content="Geany 0.20" />
		<link rel="stylesheet" href="/stylesheets/foundation.css" type="text/css" />
		<link rel="stylesheet" href="/stylesheets/app.css" type="text/css" />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js" type="text/javascript">
</script></head>
	<body>
		<?php
		$content = unserialize(file_get_contents("status.dat"));
		if($content['status'] == "finished") {
		?>
	<div class="row">
				<div class="twelve columns">
					<h2>Horarios UC3M</h2>
					<hr />
				</div>
			</div>
	
		<div class="row">
			<div class="twelve columns">
				<h4>
					<?php echo $content['name'] ?>
					</h4>
				<hr />
			</div>
		</div>
		<div class="row">
			<div class="six columns">
			<ul>
				<?php
date_default_timezone_set('Europe/Madrid');
setlocale(LC_TIME, "es_ES.UTF-8");


					if(!empty($content['clases'])) {
						foreach($content['clases'] as $k=>$v) {
							echo "<li><a href='http://www.google.com/calendar/render?cid=".urlencode("http://horariosuc3m.itram.es/".$content['path'].$k).".ics' target='_blank'>[Gcal]</a> <a href=\"".$k.".ics\">".$v."</a></li>";
						}
}
						?>
				</ul>
		</div>
		<div class="four columns">
			<h4>
				¿Y ahora, qué?
			</h4>
			<p>Pincha en el enlace de cada asignatura para descargarte su horario en formato iCal.</p><p>También puedes pulsar en el link [GCal] de al lado para añadirla a tu Google Calendar.</p><p><b>¡Cuidado!</b> Estos horarios se corresponden con los que había en la web de la Universidad el <b><?php echo strftime('%A %e de %B a las %H:%M', filemtime("status.dat")); ?></b>, cuando fueron generados.</p><p>Para tener los horarios actuales, tendrás que generarlos otra vez, para ello, <a href="/">pulsa aquí</a>.</p><p>Se generarán en esta misma dirección, por lo que si los tienes añadidos directamente a Google Calendar o a otra aplicación por su dirección, se actualizarán automáticamente (puede que tarde un ratito)</p>
		</div><script src="/javascripts/foundation.js" type="text/javascript">
</script><script src="/javascripts/app.js" type="text/javascript">
</script><?php
			}else{
			?>
	<div class="row">
				<div class="twelve columns">
					<h2>Horarios UC3M</h2>
					<hr />
				</div>
			</div>
		<div class="row">
			<h1>
				Tu horario se está generando.<br />
				Espera, por favor..
			</h1>
		<meta http-equiv="refresh" content="3" />

		</div><?php
		}
		?>
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
