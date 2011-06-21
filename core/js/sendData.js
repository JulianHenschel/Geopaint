var xhttp;
var query_case = 1;
var p_count = 10;
var distance = 25;

/* 
------------------------------------------------------------------------------------------------------
sending data to server
------------------------------------------------------------------------------------------------------
*/
function loadAjax() {
	if (window.ActiveXObject) { 
		try { 
			// IE 6 and higher
			xhttp = new ActiveXObject("MSXML2.XMLHTTP");
		} catch (e) {
			try {
				// IE 5
				xhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				xhttp=false;
			}
		}
	}
	else if (window.XMLHttpRequest) {
		try {
			// Mozilla, Opera, Safari ...
			xhttp = new XMLHttpRequest();
		} catch (e) {
			xhttp=false;
		}
	}
}
function sendData(url,responsefunction) {
	loadAjax();
	if(xhttp) {
		xhttp.open("GET", url, true);
		if(responsefunction) {
        	xhttp.onreadystatechange = responsefunction;
		}
        xhttp.send(null);
	} else {
		alert("Objekt konnte nicht instanziert werden.");
	}
}
/* 
------------------------------------------------------------------------------------------------------
get data from server
------------------------------------------------------------------------------------------------------
*/
$(function(){
	//project selection
	$('#project_selection').bind('pageAnimationEnd', function(e, info){
		if (info.direction == 'in') {
			$("#project_list").append($('<ul><center><img style="padding:5px;" src="'+domain+'/templates/img/loading.gif" /></center></ul>').
				load(domain+"/core/ajax/loadProjects.php?query_case="+query_case+"&p_count="+p_count+"&lat="+geodata["latitude"]+"&lng="+geodata["longitude"]+"&dis="+distance));
		}
		if (info.direction == 'out') {
			$("#project_list").html('');
			$("#getMoreProjects").css("visibility","visible");
		}
	});
	$('#project_selection').bind('pageAnimationEnd', function(e, data){
		if (data.direction == 'in'){
			switch (query_case) {
				case 1: 
					$("#project_selection_h2").html($("#p_case_1").html());
				break;
				case 2: 
					$("#project_selection_h2").html($("#p_case_2").html());
				break;
				case 3: 
					$("#project_selection_h2").html($("#p_case_3").html()+' ('+distance+'km)');
				break;
				case 4: 	
					$("#project_selection_h2").html($("#p_case_4").html());
			 	break;
			}
		}
	});
	//load project information
	$('#gmap').bind('swipe', function(event, info){
		jQT.goTo('#project_information', 'flip');
	});
	$('#project_information').bind('pageAnimationEnd', function(e, info){
	
		if (info.direction == 'in') {
			$("#project_info_content").append($('<div><center><img style="padding:5px;" src="'+domain+'/templates/img/loading.gif" /></center></div>').
			load(domain+"/core/ajax/projectInfo.php?id="+project_id));
		}else {
			$("#project_info_content").html('');
		}
	});
});
function moreProjects() {
	$("#project_list").append($('<ul><center><img style="margin:10px;" src="'+domain+'/templates/img/loading.gif" /></center></ul>').
	load(domain+"/core/ajax/loadProjects.php?query_case="+query_case+"&p_count="+p_count+"&lat="+geodata["latitude"]+"&lng="+geodata["longitude"]+"&dis="+distance+"&add=true", function(response) {
		if(response == '') {
			$("#getMoreProjects").css("visibility","hidden");
		}
	}));
}
