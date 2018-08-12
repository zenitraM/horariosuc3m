<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<? include("tools.php" ); ?>
<head>
	<title>Horarios UC3M</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.20" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
	<link rel="stylesheet" href="stylesheets/foundation.css">
	<link rel="stylesheet" href="stylesheets/app.css">
	
	<!--[if lt IE 9]>
		<link rel="stylesheet" href="../../stylesheets/ie.css">
	<![endif]-->
	
	<!-- IE Fix for HTML5 Tags -->
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js"></script>
	<script>
			var downdata;	
	function keysort() {
		var my_options = $("#plan option");

		my_options.sort(function(a,b) {
		    if (a.text > b.text) return 1;
		    else if (a.text < b.text) return -1;
		    else return 0
		})

		$("#plan").empty().append( my_options );
	}
	$(function() {
		jQuery.getJSON("data/data.json", function(data) {
			downdata = data
			$.each(downdata.tree, function(key,d) {
				$('#plan')
					.append($('<option>', { value : key })
					.text(downdata.planes[key]+" (plan "+key+")"))
			});
			keysort();
			$('#plan').change()

		});
		$('#plan').change(function() {
			plan = $('#plan option:selected').val()
			$('#centro').find("option").remove()
			$.each(downdata.tree[plan], function(key,d) {

				$('#centro')
					.append($('<option>', { value : key })
					.text(downdata.centros[key]))
			});
			$('#centro').change();
		});
		
		$('#centro').change(function() {
			plan = $('#plan option:selected').val()
			centro = $('#centro option:selected').val()
			$('#curso').find("option").remove()
			$.each(downdata.tree[plan][centro], function(key,d) {
				$('#curso')
					.append($('<option>', { value : key })
					.text(key+"º Curso"))
			});
				$('#curso').change();
		});
		
		$('#curso').change(function() {
			plan = $('#plan option:selected').val()
			centro = $('#centro option:selected').val()
			curso = $('#curso option:selected').val()
			$('#grupo').find("option").remove()
			$.each(downdata.tree[plan][centro][curso], function(key,d) {
				$('#grupo')
					.append($('<option>', { value : key })
					.text("Grupo " +key))
			});
			$('#grupo').change();
		});
		
		$('#grupo').change(function() {
			plan = $('#plan option:selected').val();
			centro = $('#centro option:selected').val();
			curso = $('#curso option:selected').val();
			grupo = $('#grupo option:selected').val();
			$('#cuatri').find("option").remove()
			$.each(downdata.tree[plan][centro][curso][grupo], function(key,d) {
				$('#cuatri')
					.append($('<option>', { value : d })
					.text(d+"º cuatrimestre"))
			});
		});
		
	});
	</script>
</head>

<body>
	<div class="row">
				<div class="twelve columns">
					<h2>Horarios UC3M - curso <? $academic_year = get_academic_year(); echo $academic_year; echo "/"; echo ($academic_year+1); ?></h2>
					<p>Convierte fácilmente tus horarios a formato <b>iCalendar</b>.<i>Y que salir de una práctica a las 9 sea el mayor de tus problemas.</i></p>
					<hr />
				</div>
			</div>
	<div class="row">
		<div class="eight columns">
	Dime qué horarios quieres que genere:
	<form action="preview.php" method="post" class="nice" style="display: inline">
	<select name="plan" id="plan" class="twelve columns"></select>
	<div class="row">
	<select name="centro" id="centro" class="three columns"></select>
	<select name="curso" id="curso" class="three columns"></select>
	<select name="grupo" id="grupo" class="three columns"></select>
	<select name="cuatri" id="cuatri" class="three columns"></select></div></p>
</div>
	<div class="four columns"><br><input type="submit" value="Continuar ->" class="large button"/></form></div>
	<hr>
	<div class="row"><div class="four columns">
	<h5>¿Para qué sirve esto?</h5>
	<p>Esta web te convertirá los horarios de cualquier asignatura impartida de la UC3M <i>(y que aparezca en la aplicación de horarios)</i> a un tipo de archivo llamado <b>iCalendar.</b></p>
	<p>Los archivos iCalendar pueden luego ser importados en Google Calendar e iCal, lo que te permitirá tener el calendario sincronizado en tu móvil.</p>
	</div>
	<div class="four columns">
		<h5>¿Cómo? ¿Cuándo? ¿Por qué?</h5>
		<p>Simplemente elige la carrera, el grupo y el cuatrimestre en el que estés interesado, y pulsa Continuar.</p>
		<p>Si no funciona, intentalo de nuevo en unos minutos. Mientras que convertir el horario de formato tarda muy poco, la aplicación de la universidad en ocasiones tarda unos minutos en generar el horario.</p> 
		<p>No me hago responsable de prácticas perdidas, exámenes suspensos o viajes de 2 horas de duración hechos para nada. El horario debería salir exáctamente igual al que aparece <a href="http://aplicaciones.uc3m.es/horarios-web/">aquí</a> (clases coincidentes y horarios ilógicos incluidos). Si no lo es, avísame y lo intento arreglar.</p>
		</div>
		<div class="four columns"><h5>¿Quién?</h5>
			<p><a href="http://zen.itram.es">Un antiguo estudiante de Telemática</a> que se aburría en época de exámenes. Todos sabemos que en exámenes es cuando más productivo se es en otras cosas que no sean estudiar.</p>
			<p>Si tienes algun problema, mándame un e-mail a: zen arroba itram punto e, o también estoy en Twitter: <a href="twitter.com/iamzenitram">@iamzenitraM</a></p>
			<p>Este programa está desarrollado en PHP usando <a href="http://code.google.com/p/phpquery/">phpQuery</a> para analizar el horario y <a href="http://www.kigkonsult.se/iCalcreator/">iCalCreator</a> para crear el archivo iCalendar.</P>
			<p>Toda la información ha sido obtenida de diferente lugares públicos de la web de la UC3M, como <a href="http://aplicaciones.uc3m.es/horarios-web/publicacion/principal.page">Horarios-Web</a> y <a href="https://aplicaciones.uc3m.es/cpa/cpa/generarReport.do">la consulta de programas de asignaturas</a> (para los nombres cortos).</p>
			<p>Esta web no tiene relación oficial alguna con la UC3M, salvo la de su dueño, que la <strike>sufre</strike>sufrió todos los días. Pero mereció la pena.</p> </div>
	</div>
	
	<script src="javascripts/foundation.js"></script>
	<script src="javascripts/app.js"></script>
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
