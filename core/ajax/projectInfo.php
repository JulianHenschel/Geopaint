<?PHP

include "../../config/header.config.php";

if(empty($_GET["id"])) {
	die("error. no valid id");
}

$pid = $crypt->decrypt($_GET["id"]);

$query = $db->query("SELECT id,password,name,location,date,(SELECT count(id) FROM geodata WHERE pid = ".(int) $pid.") as count_points FROM projects WHERE id = ".(int) $pid."");
$fetch = $db->fetch_array($query);

if(empty($fetch["name"])) {
	$fetch["name"] = $_GET["id"];
}

echo '<h2>'.$fetch["name"].'</h2>';

echo 	'<ul style="font-size:13px;">
			<li>Erstellt: '.$fetch["date"].'</li>
			<li>Link: '.DOMAIN.'/paint/'.$_GET["id"].'</li>
			<li>Standort: '.$fetch["location"].'</li>
			<li>'.$fetch["count_points"].' gespeicherte Punkte</li>
		</ul>';

if(!empty($fetch["password"])) {
	echo '<div class="info"><a href="#" onClick="deleteProject();" id="deleteProject_Button" class="redButton">Projekt löschen</a></div>';
}else {
	echo '<div class="info">Projekte ohne Passwort sind öffentlich und können darum nicht gelöscht werden.</div>';
}

?>