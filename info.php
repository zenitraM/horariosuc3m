<?php
if (strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false) die();
include("phpQuery-onefile.php");
function convertUrlQuery($query) { 
    $queryParts = explode('&', $query); 
    $params = array(); 
    foreach ($queryParts as $param) { 
        $item = explode('=', $param); 
        $params[$item[0]] = $item[1]; 
    }
    return $params; 
}

function file_post_contents($url,$headers=false) {
    $url = parse_url($url);

    if (!isset($url['port'])) {
        if ($url['scheme'] == 'http') { $url['port']=80; }
        elseif ($url['scheme'] == 'https') { $url['port']=443; }
    }
    $url['query']=isset($url['query'])?$url['query']:'';

    $url['protocol']=$url['scheme'].'://';
    $eol="\r\n";

    $headers =  "POST ".$url['protocol'].$url['host'].$url['path']." HTTP/1.0".$eol. 
             "Host: ".$url['host'].$eol. 
             "Referer: ".$url['protocol'].$url['host'].$url['path'].$eol. 
             "Content-Type: application/x-www-form-urlencoded".$eol. 
             "Content-Length: ".strlen($url['query']).$eol.
             $eol.$url['query'];
    $fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30); 
    if($fp) {
        fputs($fp, $headers);
        $result = '';
        while(!feof($fp)) { $result .= fgets($fp, 128); }
        fclose($fp);
        if (!$headers) {
            //removes headers
            $pattern="/^.*\r\n\r\n/s";
            $result=preg_replace($pattern,'',$result);
        }
        return $result;
    }
}
phpQuery::newDocumentFileHTML('https://aplicaciones.uc3m.es/horarios-web/publicacion/principal.page');

if(!file_exists("data/planes.dat")) {
    $planes = array();
    foreach(pq(".plan") as $plan) {
        $txt = pq($plan)->text();
        $id = explode("(Plan:", $txt);
        $id = explode(")", $id[1]);
        $id = $id[0];
        $tit = explode("► ", $txt);
        $tit = explode(" (Plan:", $tit[1]);
        $tit = $tit[0];
        $planes[$id] = $tit;
    }
    var_dump($planes);

    file_put_contents("data/planes.dat",serialize($planes));
}else{
    $planes = unserialize(file_get_contents("data/planes.dat"));
}
if(!file_exists("data/codigos.dat")) {
    $codigos = array();
    foreach($planes as $k=>$v) {
        $url = 'https://aplicaciones.uc3m.es/cpa/findAsignaturas.ajax';
        $data = array('ano' => '2016', 'codPlan' => $k, "_" => "");
 
        // use key 'http' even if you send the request to https://...
        $options = array('http' => array(
            'method'  => 'POST',
            'content' => http_build_query($data)
        ));
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
 
        $data = (json_decode($result));
    
        foreach($data->datos as $d) {
            echo $d->codigo." = ".$d->denominacion."\n";
            $codigos[$d->codigo] = $d->denominacion;
        }
    }
    file_put_contents("data/codigos.dat",serialize($codigos));
}else{
    $codigos = unserialize(file_get_contents("data/codigos.dat"));
}

//planCentro
if(!file_exists("data/tree.dat")) {
    $urlsPlanesCent = array();
    foreach(pq(".enlacePlanCentro") as $linkPlan) {
        $lp = pq($linkPlan);
        $urlsPlanesCent[] = $lp->attr("href");
    }
    var_dump($urlsPlanesCent);
    $urlsF = array();
    $tree = array();
    foreach($urlsPlanesCent as $url) {
        phpQuery::newDocumentFileHTML('http://aplicaciones.uc3m.es/'.$url);
        foreach(pq(".enlaceCuatr") as $cuatr) {
            $cuatr = pq($cuatr);
            if(strpos($cuatr->attr("href"), "grupo") && !strpos($cuatr->attr("href"), "pseudo")) {
                echo $cuatr->attr("href")."\n";
                $url = $cuatr->attr("href");
                $parts = parse_url($url);
                $dat = convertUrlQuery($parts['query']);
                $tree[$dat['plan']][$dat['centro']][$dat['curso']][$dat["grupo"]][] = $dat["valorPer"];
                $urlsF = $cuatr->attr("href");
            }
        }
    }
    var_dump($tree);
    file_put_contents("data/tree.dat", serialize($tree));

}else{
    $tree = unserialize(file_get_contents("data/tree.dat"));
}

$json = array();
$json['planes'] = $planes;
$json['tree'] = $tree;

$json['centros'] = json_decode('{"6":"Colmenarejo","7":"Getafe","8":"Colmenarejo","1":"Getafe","2":"Leganés","3":"Getafe"}');
file_put_contents('data/data.json', json_encode($json));

?>