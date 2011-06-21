<?PHP

function relativeTime($timestamp){
	
	$difference = time() - $timestamp;
	$periods = array("Sekunde", "Minute", "Stunde", "Tag", "Woche", "Monat", "Jahr", "Jahrzehnt");
	$lengths = array("60","60","24","7","4.35","12","10");
	
	if ($difference > 0) { // this was in the past
		$starting = "vor";
	} else { // this was in the future
		$difference = -$difference;
		$starting = "in";
	}
	
	$j = 0;
	while ($difference >= $lengths[$j] && $j < count($lengths)) {
		$difference /= $lengths[$j];
		$j++;
	}
	
	$difference = round($difference);
	if($difference != 1) {
		
		//$periods[$j] .= ($periods[$j] !="Tag") ? "n":"en";
		
		if($periods[$j] == "Tag" || $periods[$j] == "Monat" || $periods[$j] == "Jahr") {
			$periods[$j] .= "en";
		}else {
			$periods[$j] .= "n";
		}
	}
	$text = "$starting $difference $periods[$j]";
	
	return $text;
}

?>