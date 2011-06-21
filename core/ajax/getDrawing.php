<?PHP

include "../../config/header.config.php";

if(empty($_GET["id"])) {
	die("false");
}

$pid = $crypt->decrypt($_GET["id"]);

if(!is_numeric($pid)) {
	die("false");
}

if($pid == 85) $query = $db->query("SELECT latitude,longitude,session,color,size FROM geodata ORDER by session, time ASC");
else $query = $db->query("SELECT latitude,longitude,session,color,size FROM geodata WHERE pid = ".(int) $pid." ORDER by session, time ASC");


while($data = $db->fetch_array($query)) {
	$array[] = $data["latitude"].','.$data["longitude"].','.$data["session"].','.$data["color"].','.$data["size"];
}

if(!empty($array)) {
	echo implode(";", $array);
}

?>
