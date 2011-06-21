<?PHP

if(empty($_GET["latitude"]) || empty($_GET["longitude"])) {
	die("false");
}

include "../../config/header.config.php";

$pid = $crypt->decrypt($_GET["id"]);

// check if point is already in database
$check_query = $db->query("SELECT latitude,longitude FROM geodata WHERE pid = ".(int) $pid." AND session = '".session_id()."' ORDER by time DESC LIMIT 0,1");
$fetch_query = $db->fetch_array($check_query);

if($fetch_query["latitude"] != $_GET["latitude"] && $fetch_query["longitude"] != $_GET["longitude"]) {

	// save new point
	$db->query("INSERT into geodata SET
					latitude = '".mysql_real_escape_string($_GET["latitude"])."',
					longitude = '".mysql_real_escape_string($_GET["longitude"])."',
					altitude = '".mysql_real_escape_string($_GET["altitude"])."',
					accuracy = '".mysql_real_escape_string($_GET["accuracy"])."',
					heading = '".mysql_real_escape_string($_GET["heading"])."',
					speed = '".mysql_real_escape_string($_GET["speed"])."',
					pid = ".(int) mysql_real_escape_string($pid).",
					size = ".(int) mysql_real_escape_string($_GET["size"]).",
					color = '".mysql_real_escape_string($_GET["color"])."',
					session = '".session_id()."'");
}

// read geodata from database
$query = $db->query("SELECT latitude,longitude,session,color,size FROM geodata WHERE pid = ".(int) $pid." ORDER by session, time ASC");

while($data = $db->fetch_array($query)) {
	$array[] = $data["latitude"].','.$data["longitude"].','.$data["session"].','.$data["color"].','.$data["size"];
}

// return database result as string
if(!empty($array)) {
	echo implode(";", $array);
}
?>