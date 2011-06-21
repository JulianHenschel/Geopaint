<?PHP

include "../../config/header.config.php";

$db->query("INSERT into projects SET
				name = '".mysql_real_escape_string($_GET["pname"])."',
				password = '".mysql_real_escape_string($_GET["ppassword"])."',
				location = '".mysql_real_escape_string($_GET["location"])."'");

$insert_id = $db->insert_id();
echo $crypt->encrypt($insert_id);

?>