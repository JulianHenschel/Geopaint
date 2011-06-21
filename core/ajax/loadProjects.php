<?PHP

include "../../config/header.config.php";

if(isset($_GET["add"])) {
	$pcount_start = $_SESSION["pcount"];
	$_SESSION["pcount"] = $pcount_start + $pcount_start;	
}else {
	$pcount_start = 0;
	$_SESSION["pcount"] = $_GET["p_count"];
}

switch($_GET["query_case"]) {
	case 1:
		//Neueste Projekte
		$h2 = "Neueste Projekte";
		$pquery = $db->query("SELECT id, name, password, UNIX_TIMESTAMP(date) as date, location, (SELECT count(id) FROM geodata WHERE pid = projects.id) as geopoints FROM projects WHERE deleted != 1 ORDER by date DESC LIMIT ".(int) $pcount_start.",".(int) $_GET["p_count"]."");
	break;
	case 2:
		//Projekte mit den meisten Punkten
		$h2 = "Mit den meisten Punkten";
		$pquery = $db->query("SELECT id, name, password, UNIX_TIMESTAMP(date) as date, location, (SELECT count(id) FROM geodata WHERE pid = projects.id) as geopoints FROM projects WHERE deleted != 1 ORDER by geopoints DESC, date DESC LIMIT ".(int) $pcount_start.",".(int) $_GET["p_count"]."");
	break;
	case 3:
		//Projekte in der Nähe
		$_GET["lat"] = mysql_real_escape_string($_GET["lat"]);
		$_GET["lng"] = mysql_real_escape_string($_GET["lng"]);
		
		$pquery_firt = $db->query("SELECT 
										pid,
										( 6371 * acos( cos( radians(".$_GET["lat"].") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".mysql_real_escape_string($_GET["lng"]).") ) + sin( radians(".mysql_real_escape_string($_GET["lat"]).") ) * sin( radians( latitude ) ) ) ) AS distance
									FROM 
										geodata
									HAVING 
										distance < ".(int) $_GET["dis"]."
									ORDER by
										distance");	
										
		while($data = $db->fetch_array($pquery_firt)) {
			$new_pid [] = $data["pid"];
		}
		
		if(isset($new_pid)) {
		
			$where = implode(" OR id =", array_unique($new_pid));
		
			$pquery = $db->query("SELECT 
									id, name, password, UNIX_TIMESTAMP(date) as date, location, 
									(SELECT count(id) FROM geodata WHERE pid = projects.id) as geopoints 
								FROM 
									projects 
								WHERE
									deleted != 1 AND
									(id = $where)
								ORDER by 
									date DESC 
								LIMIT 
									".(int) $pcount_start.",".(int) $_GET["p_count"]."");
		}
	break;
	case 4:	
		//Projekte mit den meisten Punkten
		$h2 = "Mit den meisten Punkten";
		$pquery = $db->query("SELECT id, name, password, UNIX_TIMESTAMP(date) as date, location, (SELECT count(id) FROM geodata WHERE pid = projects.id) as geopoints FROM projects WHERE deleted != 1 ORDER by viewed DESC LIMIT ".(int) $pcount_start.",".(int) $_GET["p_count"]."");		
	break;
}

if(isset($pquery)) {

	while($data = $db->fetch_array($pquery)) {
		
		if(empty($data["name"])) {
		  $data["name"] = $crypt->encrypt($data["id"]);  
		}
		
		$locked = "";
		if(!empty($data["password"])) $locked = " locked";
		
			echo '<li class="arrow'.$locked.'" id="'.$data["id"].'">
				<a href="#" onClick="editExistingProject(\''.$crypt->encrypt($data["id"]).'\');">'.$data["name"].'
				<br />
				<span style="font-size:10px;">'.relativeTime($data["date"]).'</span>
				</a>
				<small class="counter">'.$data["geopoints"].'</small>
				</li>
				';
	}
}else {
	echo "Keine Projekte gefunden";	
}
?>