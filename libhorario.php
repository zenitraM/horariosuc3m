<?php
include("phpQuery-onefile.php");
require_once( 'iCalcreator.class.php' );
$codigos = unserialize(file_get_contents('data/codigos.dat')); 

class Asignatura {
	public $cod;
	public $clases;
	public $bloques = array();
	function Asignatura($cod) {
		global $codigos;
		$this->cod = $cod;
		$this->name = $codigos[$cod];
	}

	function addBlock($pqc) {
		$mag = $pqc->find(".estudioMagistral");
		$magistral = (count($mag) == 1);
		$horas = $pqc->find('[style="font-size: 8pt; font-weight: bold;color: maroon"]');
		$bloques = $pqc->find('.fechasSesion');
		$fechas = $bloques->find(".fechas");
		$aulas = $bloques->find(".aulas");
		$num = count($fechas);
		for($c=0; $c<$num; $c++) {
			//	echo "\n".$fechas->eq($c)->text()." > ".$aulas->eq($c)->text();
			$this->bloques[] = new Bloque($fechas->eq($c), $aulas->eq($c), $horas, $magistral);
		}
	}

	function iCal($folder, $name) {
		$config = array( 'unique_id' => 'horariosuc3m.itram.es' );
		// set Your unique id
		$v = new vcalendar( $config );
		// create a new calendar instance

		$v->setProperty( 'method', 'PUBLISH' );
		// required of some calendar software
		$v->setProperty( "x-wr-calname", $this->name );
		// required of some calendar software
		$v->setProperty( "X-WR-CALDESC", $this->name );
		// required of some calendar software
		$v->setProperty( "X-WR-TIMEZONE", "Europe/Madrid" );
		foreach($this->bloques as $b) {
			$this->addBloque($v, $b);
		}
		$config = array( 'directory' => $folder, 'filename' => $name);
		$v->setConfig( $config );
		// set output directory and file name
		$v->saveCalendar();
	}

	function addBloque($v, $bloque) {
		$vevent = & $v->newComponent( 'vevent' );
		$year = 2017;
		if($bloque->fechas[0][1] > 7) $year = 2016;
		// create an event calendar component
		$start = array( 'year'=>$year, 'month'=>$bloque->fechas[0][1], 'day'=>$bloque->fechas[0][0], 'hour'=>$bloque->horas[0][0], 'min'=>$bloque->horas[0][1], 'sec'=>0 );
		$vevent->setProperty( 'dtstart', $start );
		$end = array( 'year'=>$year, 'month'=>$bloque->fechas[0][1], 'day'=>$bloque->fechas[0][0], 'hour'=>$bloque->horas[1][0], 'min'=>$bloque->horas[1][1], 'sec'=>0);
		$vevent->setProperty( 'dtend', $end );
		$vevent->setProperty( 'LOCATION', $bloque->lugar );
		$vevent->setProperty( "rrule", array('FREQ'=>'WEEKLY', 'UNTIL'=>array( 'year'=>$year, 'month'=>$bloque->fechas[1][1], 'day'=>$bloque->fechas[1][0], 'hour'=>$bloque->horas[1][0], 'min'=>$bloque->horas[1][1], 'sec'=>0)));

		// property name - case independent
		$b = "";
		if($bloque->magistral) $b = "[MAG] ";
		$vevent->setProperty( 'summary', $b.$this->name );
	}
}

class Bloque {
	function parseDate($date) {
		$fec = array("ene","feb","mar","abr","may","jun","jul","ago","sep","oct","nov","dic");
		$s = explode("\.", $date);
		$s[1] = str_replace(":","",$s[1]);
		return array($s[0], array_search($s[1], $fec)+1);
	}
	function Bloque($fecha, $aula, $horas, $mag) {
		$this->magistral = $mag;
		$hrs = explode(" a ",$horas->text());
		$this->horas = array(explode(":",$hrs[0]), explode(":",$hrs[1]));
		$this->lugar = $aula->text();
		$date = $fecha->text();
		if(!strpos($date, "-")) {
			$dates = array($date, $date);
		}else{
			$dates = explode("-", $date);
		}
		$this->fechas = array($this->parseDate($dates[0]), $this->parseDate($dates[1]));
	}

}


function rflush (){
    echo(str_repeat(' ',256));
    // check that buffer is actually set before flushing
    if (ob_get_length()){            
        @ob_flush();
        @flush();
        @ob_end_flush();
    }    
}
function parseHorario($idh) {
	global $codigos;
	$url = $idh->getURL();


	$folder = $idh->getFolder();
	echo $folder;
	mkdir($folder, 0777, true);
	file_put_contents($folder."/index.php", '<?php include("../../../../../../lister.php"); ?>');
	file_put_contents($folder."/status.dat", serialize(array('status'=>'proc')));
	header('Location: /'.$idh->getFolder());
	rflush();
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
	$page=curl_exec ($ch);
	curl_close ($ch);

	if($page==false) {
		file_put_contents($folder."/status.dat", serialize(array('status'=>'err')));
		die();
	}

	$infoar = array('status'=>'finished');
	$doc = phpQuery::newDocumentHTML($page);
	$clases = array();
	foreach(pq(".celdaConSesion") as $celda) {
		$pqc = pq($celda);
		$name = $pqc->find(".asignaturaAgrupacion, .asignaturaMagistral, .asignaturaGrupo");
		$name = explode("-", $name->text());
		$cod = $name[0];
		//	echo $cod;
		//var_dump(pq($celda)->html());
		if(!array_key_exists($cod,$clases)) $clases[$cod] = new Asignatura($cod);
		$clases[$cod]->addBlock($pqc);
		$infoar['clases'][$cod] = $codigos[$cod];
	};
	$infoar['name'] = $idh->getCleanName();
	$infoar['path'] = $folder;
	//echo $folder;
	foreach($clases as $cod=>$clase) {
		$clases[$cod]->iCal($folder, $cod.".ics");
	}
	file_put_contents($folder."/status.dat", serialize($infoar));
}

class IDHorario {
	
	function IDHorario($plan, $centro, $curso, $grupo, $cuatr) {
		$this->plan = $plan;
		$this->centro = $centro;
		$this->curso = $curso;
		$this->grupo = $grupo;
		$this->cuatr = $cuatr;
	}
	function getURL() {
		return "https://aplicaciones.uc3m.es/horarios-web/publicacion/2016/porCentroPlanCursoGrupo.tt?plan=".$this->plan."&centro=".$this->centro.
																											"&curso=".$this->curso."&grupo=".$this->grupo."&tipoPer=C&valorPer=".$this->cuatr;
	}
	
	function getFolder() {
		return "out/".$this->plan."/".$this->centro."/".$this->curso."/".$this->grupo."/".$this->cuatr."/";
	}
	
	function getCleanName() {
		$centros = json_decode('{"6":"Colmenarejo","7":"Getafe","8":"Colmenarejo","1":"Getafe","2":"Leganés","3":"Getafe"}');
		$planes = unserialize(file_get_contents("data/planes.dat"));
		$plan = $this->plan; $centro = $this->centro;
		$titulo = $planes[$plan].", ".$centros->$centro."<br>".$this->curso."º curso, grupo ".$this->grupo.", ".$this->cuatr."º cuatrimestre";
		return $titulo;
	}
}
?>
