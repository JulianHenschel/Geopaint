<?PHP

include "../../config/header.config.php";

$pid = $crypt->decrypt($_GET["id"]);

$query = $db->query("SELECT password FROM projects WHERE id = ".(int) $pid."");
$fetch = $db->fetch_array($query);


if(empty($fetch["password"])) {
	echo "open";
}else {
	echo $fetch["password"];
}
?>