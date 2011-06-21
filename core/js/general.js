var update_interval, timeout_interval, latest_marker_lat, latest_marker_long, record_coords;
var polyline_coords, polyline, allow_record = false;
var update_time = 30000;

/* 
------------------------------------------------------------------------------------------------------

------------------------------------------------------------------------------------------------------
*/
$(function(){
	$('body').bind('turn', function(e, data){
		setMapSize();
	});
	$('#size a').tap(function(e){
		size = parseInt(this.innerHTML);
		$("#size_info2").html(size);
		document.getElementById('size_info1').style.width = size+'px';
		document.getElementById('size_info1').style.height = size+'px';
		document.getElementById('size_info1').style.mozBorderRadius = (size/2)+'px';
		document.getElementById('size_info1').style.webkitBorderRadius = (size/2)+'px';
		jQT.goBack();
	});
	$('#returnFromPainting').tap(function(e){
		resetProject();
		jQT.goBack();
	});
	$('#saveColor').tap(function(e){
		color = document.getElementById('selectedcolor').value.replace('#', '');
		document.getElementById('color_info').style.background = '#'+color;
		jQT.goBack();
	});
	$('#createNewProject').tap( function(e){
		createNewProject();
	});
	$('#getMoreProjects').tap( function(e){
		moreProjects();
	});
});
/* 
------------------------------------------------------------------------------------------------------
Neues Projekt erstellen
------------------------------------------------------------------------------------------------------
*/
function createNewProject() {
	
	var pname = $("#pname").val();
	var ppassword = $("#ppassword").val();
	
	if(ppassword != '') {
		ppassword = md5(ppassword);
	}
	
	sendData(domain+'/core/ajax/createNewProject.php?pname='+pname+'&ppassword='+ppassword+'&location='+locationname, handleNewProject);
  
}
function handleNewProject() {
	if (xhttp.readyState == 4) {
		
		project_id = xhttp.responseText;
		
		//Name in Toolbar setzen
		if($("#pname").val() == '') {
			var name = project_id;
		}else {
			var name = $("#pname").val();
		}
		
		$("#project_name").html(name);
		
		//add reload & accuracy info for debugging
		//document.getElementById("project_name").innerHTML += ' ↻<span id="geo_counter">0</span>'+' ~<span id="geo_accuracy">0</span>m';
				
		loadMap();
						
		jQT.goTo('#paint', 'slide');
	}
}
/* 
------------------------------------------------------------------------------------------------------
Projekt weiterführen
------------------------------------------------------------------------------------------------------
*/
function editExistingProject(id) {
    
  	project_id = id;

    if(document.getElementById("pname").value == '') {
      document.getElementById("project_name").innerHTML = project_id;
    }else {
      document.getElementById("project_name").innerHTML = document.getElementById("pname").value;
    }

    //add reload & accuracy info for debugging
    //document.getElementById("project_name").innerHTML += ' ↻<span id="geo_counter">0</span>'+' ~<span id="geo_accuracy">0</span>m';
    
    loadMap();
	
	timeout_interval = window.setTimeout('sendData(domain+\'/core/ajax/getDrawing.php?id=\'+project_id, handleGetDrawing)', 5000);
	    
    jQT.goTo('#paint', 'slide');
}
/* 
------------------------------------------------------------------------------------------------------
Punkte speichern
------------------------------------------------------------------------------------------------------
*/
function saveNewGeoPoint() {
	
	if((latest_marker_lat != geodata["latitude"]) && (latest_marker_long != geodata["longitude"]) && project_id != 0 && allow_record) {
				
		if(current_pos_marker) {
			current_pos_marker.setMap(null);
		}
		var current_pos_point = new google.maps.LatLng(geodata["latitude"],geodata["longitude"]);
		current_pos_marker = new google.maps.Marker({
			position: current_pos_point,
			map: map
			//icon: 'http://maps.google.com/mapfiles/kml/pal4/icon57.png'
		});
		
		sendData(domain+'/core/ajax/saveNewGeoPoint.php?latitude='+geodata["latitude"]+'&longitude='+geodata["longitude"]+'&altitude='+geodata["altitude"]+'&accuracy='+geodata["accuracy"]+'&heading='+geodata["heading"]+'&speed='+geodata["speed"]+'&color='+color+'&size='+size+'&id='+project_id, handleGetDrawing);
		
		latest_marker_lat = geodata["latitude"];
		latest_marker_long = geodata["longitude"];
	}
	
	//display accuracy
	if(document.getElementById("geo_accuracy")) document.getElementById("geo_accuracy").innerHTML = geodata["accuracy"];
	
	//display number of updates
	if(document.getElementById("geo_counter")) document.getElementById("geo_counter").innerHTML = parseFloat(document.getElementById("geo_counter").innerHTML) + 1;
  
}
function handleGetDrawing() {
	
	if (xhttp.readyState == 4) {
						
		var geodata = xhttp.responseText;
		
		if(geodata != '') {
			
			removePolyline();
			removeMarkers();
		
			var coords = geodata.split(";");
			var sessions = new Array();
			var session_id = 0, session_count = 0;
								
			for(var j = 0; j < coords.length; j++) {
				var data = coords[j].split(",");
							
				if(session_id != data[2]) {
					
					sessions[session_count] = new Array();
					sessions[session_count].push(data);	
					
					session_id = data[2];
					session_count++;
				}else {
					sessions[session_count-1].push(data);	
				}
			}
			
			for(var m = 0; m < sessions.length; m++) {
				var user = new Array();
				
				for(var n = 0; n < sessions[m].length; n++) {	
					user.push(sessions[m][n].join(','));				
				}			
				drawPolylines(user.join(";"));
			}
			
			var unique_marker = unique(coords);
			
			for(var i = 0; i < unique_marker.length; i++) {
				
				var latlgn = unique_marker[i].split(",");
				var new_latlgn = new google.maps.LatLng(latlgn[0],latlgn[1]);
				
				setMarker(latlgn[0],latlgn[1]);
			}
		}
		$("#sliding_image").fadeOut("slow");
	}
}
/* 
------------------------------------------------------------------------------------------------------
draw polylines in different colors
------------------------------------------------------------------------------------------------------
*/
function drawPolylines(coords) {

	var array = [];
	var color = 0, array_num = 0;
	var coords = coords.split(';');
		
	for(var i = 0; i < coords.length; i++) {
		var data = coords[i].split(",");
		if(data.length > 1) {
			array[i] = new Array();
			array[i].push(data);
		}
	}
	
	for(var i = 0; i < array.length; i++) {
		if(array.length > 1) {
			
			var polyline_coords = new google.maps.MVCArray();
					
			if(i > 0) {

				var latlng = new google.maps.LatLng(array[i][0][0],array[i][0][1]);
				var latlng_last = new google.maps.LatLng(array[i-1][0][0],array[i-1][0][1]);
				
				polyline_coords.insertAt(polyline_coords.length, latlng_last);
				polyline_coords.insertAt(polyline_coords.length, latlng);
				
				setPolyline(polyline_coords, array[i][0][4], 1.0, "#"+array[i][0][3]);
			}
		}
	}
}
/* 
------------------------------------------------------------------------------------------------------
Daten aufzeichnen + Neue Session generieren + Passwort abfragen
------------------------------------------------------------------------------------------------------
*/
function recordStatus() {
	
	record_coords = document.getElementById("record").checked;
	
	if(record_coords == true) {
		if(allow_record) {
			return true;
		}else {
	  		return sendData(domain+'/core/ajax/getPassword.php?id='+project_id, handlePassword);
		}
	}else {		
		sendData(domain+'/core/ajax/regenerateId.php');
		return false;
	}
}
function handlePassword() {
	if (xhttp.readyState == 4) {	
		if(xhttp.responseText != 'open') {	
      		if(md5(prompt("Passwort:", "")) != xhttp.responseText) {
				
       		 	alert("Falsches Passwort");
				document.getElementById("record").checked = false;
				
       		 	return false;
			}else {
				
				allow_record = true;
							
				saveNewGeoPoint();
				update_interval = window.setInterval("saveNewGeoPoint()", update_time);
				
				return true;
			}
      	}else if(xhttp.responseText == 'open') {
			
			allow_record = true;
								
			saveNewGeoPoint();
			update_interval = window.setInterval("saveNewGeoPoint()", update_time);
				
			return true;
		}
	}
}
/* 
------------------------------------------------------------------------------------------------------
Projekt zurücksetzen
------------------------------------------------------------------------------------------------------
*/
function resetProject() {
	
	removePolyline();
	removeMarkers();
	
	if(current_pos_marker) {
		current_pos_marker.setMap(null);
	}
	
	//Eingabefelder
	document.getElementById("pname").value = '';
	document.getElementById("ppassword").value = '';
	
	//Projektname
	document.getElementById("project_name").value = '';
	
	//Update Interval beenden
	window.clearInterval(update_interval);
	window.clearTimeout(timeout_interval);
	
	//Passwortabfrage
	allow_record = false;
	
	//Checkbox deaktivieren
	document.getElementById("record").checked = false;
	
	latest_marker_lat = '';
	latest_marker_long = '';
	
	//Projekt, Pinsel und Farbe
	project_id = 0;
}
/* 
------------------------------------------------------------------------------------------------------
delete project
------------------------------------------------------------------------------------------------------
*/
function deleteProject() {
	
	var password = prompt("Passwort:", "");
	
	if(password != '') {
		sendData(domain+'/core/ajax/deleteProject.php?id='+project_id+'&pwd='+md5(password), handledeleteProject);
	}
}
function handledeleteProject() {
	if (xhttp.readyState == 4) {
		
		var dp_return = xhttp.responseText;
		
		if(dp_return == 'true') {
			
			resetProject();
			jQT.goTo('#starting_options');
		}else {
			alert("Falsches Passwort");
		}
	}
}
/* 
------------------------------------------------------------------------------------------------------
Doppelte Einträge aus Array löschen
Quelle: http://www.roseindia.net/java/javascript-array/javascript-array-unique.shtml
------------------------------------------------------------------------------------------------------
*/
function unique(arrayName) {
	
	var newArray=new Array();
	label:for(var i=0; i<arrayName.length;i++ ) {  
		for(var j=0; j<newArray.length;j++ ) {
			if(newArray[j]==arrayName[i]) 
			continue label;
		}
		newArray[newArray.length] = arrayName[i];
	}
	return newArray;
}
/* 
------------------------------------------------------------------------------------------------------
MD5 Hash
Quelle: http://phpjs.org/functions/md5:469
------------------------------------------------------------------------------------------------------
*/
function md5 (str) {
    // http://kevin.vanzonneveld.net
    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // + namespaced by: Michael White (http://getsprink.com)
    // +    tweaked by: Jack
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // -    depends on: utf8_encode
    // *     example 1: md5('Kevin van Zonneveld');
    // *     returns 1: '6e658d4bfcb59cc13f96c14450ac40b9'

    var xl;

    var rotateLeft = function (lValue, iShiftBits) {
        return (lValue<<iShiftBits) | (lValue>>>(32-iShiftBits));
    };

    var addUnsigned = function (lX,lY) {
        var lX4,lY4,lX8,lY8,lResult;
        lX8 = (lX & 0x80000000);
        lY8 = (lY & 0x80000000);
        lX4 = (lX & 0x40000000);
        lY4 = (lY & 0x40000000);
        lResult = (lX & 0x3FFFFFFF)+(lY & 0x3FFFFFFF);
        if (lX4 & lY4) {
            return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
        }
        if (lX4 | lY4) {
            if (lResult & 0x40000000) {
                return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
            } else {
                return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
            }
        } else {
            return (lResult ^ lX8 ^ lY8);
        }
    };

    var _F = function (x,y,z) { return (x & y) | ((~x) & z); };
    var _G = function (x,y,z) { return (x & z) | (y & (~z)); };
    var _H = function (x,y,z) { return (x ^ y ^ z); };
    var _I = function (x,y,z) { return (y ^ (x | (~z))); };

    var _FF = function (a,b,c,d,x,s,ac) {
        a = addUnsigned(a, addUnsigned(addUnsigned(_F(b, c, d), x), ac));
        return addUnsigned(rotateLeft(a, s), b);
    };

    var _GG = function (a,b,c,d,x,s,ac) {
        a = addUnsigned(a, addUnsigned(addUnsigned(_G(b, c, d), x), ac));
        return addUnsigned(rotateLeft(a, s), b);
    };

    var _HH = function (a,b,c,d,x,s,ac) {
        a = addUnsigned(a, addUnsigned(addUnsigned(_H(b, c, d), x), ac));
        return addUnsigned(rotateLeft(a, s), b);
    };

    var _II = function (a,b,c,d,x,s,ac) {
        a = addUnsigned(a, addUnsigned(addUnsigned(_I(b, c, d), x), ac));
        return addUnsigned(rotateLeft(a, s), b);
    };

    var convertToWordArray = function (str) {
        var lWordCount;
        var lMessageLength = str.length;
        var lNumberOfWords_temp1=lMessageLength + 8;
        var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1 % 64))/64;
        var lNumberOfWords = (lNumberOfWords_temp2+1)*16;
        var lWordArray=new Array(lNumberOfWords-1);
        var lBytePosition = 0;
        var lByteCount = 0;
        while ( lByteCount < lMessageLength ) {
            lWordCount = (lByteCount-(lByteCount % 4))/4;
            lBytePosition = (lByteCount % 4)*8;
            lWordArray[lWordCount] = (lWordArray[lWordCount] | (str.charCodeAt(lByteCount)<<lBytePosition));
            lByteCount++;
        }
        lWordCount = (lByteCount-(lByteCount % 4))/4;
        lBytePosition = (lByteCount % 4)*8;
        lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80<<lBytePosition);
        lWordArray[lNumberOfWords-2] = lMessageLength<<3;
        lWordArray[lNumberOfWords-1] = lMessageLength>>>29;
        return lWordArray;
    };

    var wordToHex = function (lValue) {
        var wordToHexValue="",wordToHexValue_temp="",lByte,lCount;
        for (lCount = 0;lCount<=3;lCount++) {
            lByte = (lValue>>>(lCount*8)) & 255;
            wordToHexValue_temp = "0" + lByte.toString(16);
            wordToHexValue = wordToHexValue + wordToHexValue_temp.substr(wordToHexValue_temp.length-2,2);
        }
        return wordToHexValue;
    };

    var x=[],
        k,AA,BB,CC,DD,a,b,c,d,
        S11=7, S12=12, S13=17, S14=22,
        S21=5, S22=9 , S23=14, S24=20,
        S31=4, S32=11, S33=16, S34=23,
        S41=6, S42=10, S43=15, S44=21;

    str = this.utf8_encode(str);
    x = convertToWordArray(str);
    a = 0x67452301; b = 0xEFCDAB89; c = 0x98BADCFE; d = 0x10325476;
    
    xl = x.length;
    for (k=0;k<xl;k+=16) {
        AA=a; BB=b; CC=c; DD=d;
        a=_FF(a,b,c,d,x[k+0], S11,0xD76AA478);
        d=_FF(d,a,b,c,x[k+1], S12,0xE8C7B756);
        c=_FF(c,d,a,b,x[k+2], S13,0x242070DB);
        b=_FF(b,c,d,a,x[k+3], S14,0xC1BDCEEE);
        a=_FF(a,b,c,d,x[k+4], S11,0xF57C0FAF);
        d=_FF(d,a,b,c,x[k+5], S12,0x4787C62A);
        c=_FF(c,d,a,b,x[k+6], S13,0xA8304613);
        b=_FF(b,c,d,a,x[k+7], S14,0xFD469501);
        a=_FF(a,b,c,d,x[k+8], S11,0x698098D8);
        d=_FF(d,a,b,c,x[k+9], S12,0x8B44F7AF);
        c=_FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);
        b=_FF(b,c,d,a,x[k+11],S14,0x895CD7BE);
        a=_FF(a,b,c,d,x[k+12],S11,0x6B901122);
        d=_FF(d,a,b,c,x[k+13],S12,0xFD987193);
        c=_FF(c,d,a,b,x[k+14],S13,0xA679438E);
        b=_FF(b,c,d,a,x[k+15],S14,0x49B40821);
        a=_GG(a,b,c,d,x[k+1], S21,0xF61E2562);
        d=_GG(d,a,b,c,x[k+6], S22,0xC040B340);
        c=_GG(c,d,a,b,x[k+11],S23,0x265E5A51);
        b=_GG(b,c,d,a,x[k+0], S24,0xE9B6C7AA);
        a=_GG(a,b,c,d,x[k+5], S21,0xD62F105D);
        d=_GG(d,a,b,c,x[k+10],S22,0x2441453);
        c=_GG(c,d,a,b,x[k+15],S23,0xD8A1E681);
        b=_GG(b,c,d,a,x[k+4], S24,0xE7D3FBC8);
        a=_GG(a,b,c,d,x[k+9], S21,0x21E1CDE6);
        d=_GG(d,a,b,c,x[k+14],S22,0xC33707D6);
        c=_GG(c,d,a,b,x[k+3], S23,0xF4D50D87);
        b=_GG(b,c,d,a,x[k+8], S24,0x455A14ED);
        a=_GG(a,b,c,d,x[k+13],S21,0xA9E3E905);
        d=_GG(d,a,b,c,x[k+2], S22,0xFCEFA3F8);
        c=_GG(c,d,a,b,x[k+7], S23,0x676F02D9);
        b=_GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);
        a=_HH(a,b,c,d,x[k+5], S31,0xFFFA3942);
        d=_HH(d,a,b,c,x[k+8], S32,0x8771F681);
        c=_HH(c,d,a,b,x[k+11],S33,0x6D9D6122);
        b=_HH(b,c,d,a,x[k+14],S34,0xFDE5380C);
        a=_HH(a,b,c,d,x[k+1], S31,0xA4BEEA44);
        d=_HH(d,a,b,c,x[k+4], S32,0x4BDECFA9);
        c=_HH(c,d,a,b,x[k+7], S33,0xF6BB4B60);
        b=_HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);
        a=_HH(a,b,c,d,x[k+13],S31,0x289B7EC6);
        d=_HH(d,a,b,c,x[k+0], S32,0xEAA127FA);
        c=_HH(c,d,a,b,x[k+3], S33,0xD4EF3085);
        b=_HH(b,c,d,a,x[k+6], S34,0x4881D05);
        a=_HH(a,b,c,d,x[k+9], S31,0xD9D4D039);
        d=_HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);
        c=_HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);
        b=_HH(b,c,d,a,x[k+2], S34,0xC4AC5665);
        a=_II(a,b,c,d,x[k+0], S41,0xF4292244);
        d=_II(d,a,b,c,x[k+7], S42,0x432AFF97);
        c=_II(c,d,a,b,x[k+14],S43,0xAB9423A7);
        b=_II(b,c,d,a,x[k+5], S44,0xFC93A039);
        a=_II(a,b,c,d,x[k+12],S41,0x655B59C3);
        d=_II(d,a,b,c,x[k+3], S42,0x8F0CCC92);
        c=_II(c,d,a,b,x[k+10],S43,0xFFEFF47D);
        b=_II(b,c,d,a,x[k+1], S44,0x85845DD1);
        a=_II(a,b,c,d,x[k+8], S41,0x6FA87E4F);
        d=_II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);
        c=_II(c,d,a,b,x[k+6], S43,0xA3014314);
        b=_II(b,c,d,a,x[k+13],S44,0x4E0811A1);
        a=_II(a,b,c,d,x[k+4], S41,0xF7537E82);
        d=_II(d,a,b,c,x[k+11],S42,0xBD3AF235);
        c=_II(c,d,a,b,x[k+2], S43,0x2AD7D2BB);
        b=_II(b,c,d,a,x[k+9], S44,0xEB86D391);
        a=addUnsigned(a,AA);
        b=addUnsigned(b,BB);
        c=addUnsigned(c,CC);
        d=addUnsigned(d,DD);
    }

    var temp = wordToHex(a)+wordToHex(b)+wordToHex(c)+wordToHex(d);

    return temp.toLowerCase();
}
/* 
------------------------------------------------------------------------------------------------------
UTF-8 Encode
Quelle: http://phpjs.org/functions/utf8_encode:577
------------------------------------------------------------------------------------------------------
*/
function utf8_encode ( argString ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: sowberry
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // +   improved by: Yves Sucaet
    // +   bugfixed by: Onno Marsman
    // +   bugfixed by: Ulrich
    // *     example 1: utf8_encode('Kevin van Zonneveld');
    // *     returns 1: 'Kevin van Zonneveld'

    var string = (argString+''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");

    var utftext = "";
    var start, end;
    var stringl = 0;

    start = end = 0;
    stringl = string.length;
    for (var n = 0; n < stringl; n++) {
        var c1 = string.charCodeAt(n);
        var enc = null;

        if (c1 < 128) {
            end++;
        } else if (c1 > 127 && c1 < 2048) {
            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
        } else {
            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
        }
        if (enc !== null) {
            if (end > start) {
                utftext += string.substring(start, end);
            }
            utftext += enc;
            start = end = n+1;
        }
    }

    if (end > start) {
        utftext += string.substring(start, string.length);
    }

    return utftext;
}
/* 
------------------------------------------------------------------------------------------------------
DEBUG FUNCTION
------------------------------------------------------------------------------------------------------
*/
function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}