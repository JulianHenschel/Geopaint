<?PHP

$date_day = time() - 86400; // delete every project that is older than 24 hours …

$clearQuery = $db->query("SELECT
						id,
						UNIX_TIMESTAMP(date) as date,
						(SELECT count(id) FROM geodata WHERE pid = p.id) as count
					 FROM 
					 	projects p 
					 HAVING
						count <= 1"); // and has less than 2 points (no lines)

while($data = $db->fetch_array($clearQuery)) {
	if($data["id"] != 85) { // don’t delete »aggregated map«
		if($data["date"] < $date_day) {
			$db->query("DELETE FROM projects WHERE id = ".$data["id"]."");
			$db->query("DELETE FROM geodata WHERE pid = ".$data["id"]."");		
		}
	}
}

?>
