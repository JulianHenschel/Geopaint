<?PHP

include "../../config/header.config.php";

$pid = $crypt->decrypt($_GET["id"]);

$query = $db->query("SELECT password FROM projects WHERE id = ".(int) $pid."");
$fetch = $db->fetch_array($query);

if(($_GET["pwd"] == $fetch["password"]) && !empty($fetch["password"])) {
	$db->query("UPDATE projects SET deleted = 1 WHERE id = ".(int) $pid."");
	echo 'true';
}else {
	echo 'false';	
}
?>