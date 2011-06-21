<?PHP include "../../config/header.config.php"; ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>geopaint.net</title>

<?PHP
echo '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
echo '<script type="text/javascript" src="'.DOMAIN.'/core/js/general.js"></script>';
?>

</head>

<script type="text/javascript">

var map;
var bounds = new google.maps.LatLngBounds();
var marker_points = [];
var color = [];

function initialize() {
	
	//document.getElementById("map").style.width = window.innerWidth+"px";
	//document.getElementById("map").style.height = window.innerHeight+"px";
	
	var myLatlng = new google.maps.LatLng(-25.363882,131.044922);
	var myOptions = {
		zoom: 1,
		center: myLatlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	map = new google.maps.Map(document.getElementById("map"), myOptions); 
	
	overlay = new google.maps.OverlayView();
    overlay.draw = function() {};
    overlay.setMap(map);
	
	<?PHP 
	
	if(isset($_GET["id"])) {
		$where = "WHERE pid = ".(int)$_GET["id"];
	}else {
		$where = "";
	}
	
	$dbquery = $db->query("SELECT latitude,longitude,color FROM geodata $where LIMIT 1000");
	
	while($data = $db->fetch_array($dbquery)) {
		
		echo '
			var new_latlng = new google.maps.LatLng('.$data["latitude"].','.$data["longitude"].');
			var marker = new google.maps.Marker({
        		position: new_latlng, 
				flat: true,
        		map: map
    		});  
			bounds.extend(new_latlng);
			marker_points.push(new_latlng);
			
			color.push("'.$data["color"].'");
		';	
	}
	echo 'map.fitBounds(bounds);';
	
	?>
}

function getPixel() {
	
	var newspan = document.createElement("span");
	document.getElementById("list").appendChild(newspan);
	document.getElementById("list").lastChild.innerHTML = 'int[][] a_pixel = {';
	
	for(var i= 0; i < marker_points.length; i++) {
		
		var divPixel = overlay.getProjection().fromLatLngToDivPixel(marker_points[i]); 
		
		document.getElementById("list").lastChild.innerHTML += '{'+divPixel.x+','+divPixel.y+',#'+color[i]+'},';
	}
	
	var span = document.createElement("span");
	document.getElementById("list").appendChild(span);
	document.getElementById("list").lastChild.innerHTML = '};';
}

</script>

<body onLoad="initialize();">
<a href="#" onClick="getPixel();">get processing array</a>
<div id="map" style="width:900px; height:550px;"></div>
<div id="list"></div>
</body>
</html>