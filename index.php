<?PHP include "config/header.config.php"; include "translations.php"; ?>
<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8" />

<title>Geopaint</title>

<link rel="stylesheet" type="text/css" media="screen" href="<?PHP echo DOMAIN ?>/templates/jqtouch.min.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?PHP echo DOMAIN ?>/templates/theme.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?PHP echo DOMAIN ?>/templates/farbtastic.css" />

<script>var domain = "<?PHP echo DOMAIN; ?>";</script>
<script>var project_id = 0, color = "FF0000", size = 6;</script>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script src="http://code.google.com/apis/gears/gears_init.js"></script>
<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script src="<?PHP echo DOMAIN ?>/core/js/jqtouch.min.js" type="application/x-javascript"></script>

<script>var jQT = new $.jQTouch({startupScreen: domain+'/images/startup.png',statusBar: 'black',fullScreen: true,preloadImages: [domain+'/templates/img/loading.gif'],backSelector:'.cancel,.goback'});</script>

<script src="<?PHP echo DOMAIN ?>/core/js/general.min.js"></script>
<script src="<?PHP echo DOMAIN ?>/core/js/map.js"></script>
<script src="<?PHP echo DOMAIN ?>/core/js/sendData.js"></script>
<script src="<?PHP echo DOMAIN ?>/core/js/geolocation.js"></script>
<script src="<?PHP echo DOMAIN ?>/core/js/farbtastic.min.js"></script>

</head>

<body onUnload="resetProject();">
<!-- 
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
starting
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
-->
<div id="loading_app" class="current">
  <div class="toolbar">
	  <h1><?php echo $lloading; ?></h1>
	</div>
	<div class="info"><p><?php echo $linfoloading; ?></p><p style="visibility:hidden;" id="nogeo"><?php echo $lcompatibility; ?></p></div>
</div>
<!-- 
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
info
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
-->
<div id="information">
	<div class="toolbar">
		<h1><?php echo $linfo; ?></h1>
		<a class="cancel" href="#"><?php echo $lback; ?></a>
	</div>
	<h2><?php echo $lby; ?></h2>
	<ul class="rounded">
		<li class="arrow"><a href="http://inqu.at/a" target="_blank"><img src="<?php DOMAIN ?>/images/jan.png" />Jan-Christoph Borchardt</a></li>
	    <li class="arrow"><a href="http://userdesign.de/" target="_blank"><img src="<?php DOMAIN ?>/images/julian.png" />Julian Henschel</a></li>
	</ul>
	
	<h2><?php echo $lcontact; ?></h2>
	<ul class="rounded">
		<li class="arrow"><a href="http://geopaint.net/blog" target="_blank"><img src="<?php DOMAIN ?>/images/blog.png" />Blog</a></li>
	    <li class="arrow"><a href="mailto:info@geopaint.net"><img src="<?php DOMAIN ?>/images/mail.png" />Mail</a></li>
	</ul>
	
	<h2><?php echo $lthanks; ?></h2>
	<ul class="rounded">
		<li class="arrow"><a href="http://www.hdm-stuttgart.de/idb/team/tille/" target="_blank"><img src="<?php DOMAIN ?>/images/ralph.png" />Prof. Ralph Tille<br /><span><?php echo $lthanksmentoring; ?></span></a></li>
	    <li class="arrow"><a href="http://hannesfritz.de" target="_blank"><img src="<?php DOMAIN ?>/images/hannes.png" />Hannes Fritz<br /><span><?php echo $lthankstesting; ?></span></a></li>
	    <li class="arrow"><a href="http://www.buildcontext.com/blog/2010/01/05/browser-based-geolocation-experiment-powerful-mobile-web-html5/" target="_blank"><img src="<?php DOMAIN ?>/images/ben.png" />Ben Hedrington<br /><span><?php echo $lthanksgeolocation; ?></span></a></li>
	    <li class="arrow"><a href="http://biggestdrawingintheworld.com/drawing.aspx" target="_blank"><img src="<?php DOMAIN ?>/images/erik.png" />Erik Nordenankar<br /><span><?php echo $lthanksbiggestdrawing; ?></span></a></li>
	    <li class="arrow"><a href="http://www.flickr.com/photos/loso/sets/72157623804416227/" target="_blank"><img src="<?php DOMAIN ?>/images/berlin.png" /><?php echo $lthanksberlinartists; ?><br /><span><?php echo $lthanksberlin; ?></span></a></li>
	</ul>
    
	<h2><?php echo $llocation; ?></h2>
	<ul class="rounded">
		<li><img src="<?php DOMAIN ?>/images/points.png" /><span id="current_location">-</span></li>
	</ul>
</div>
<!-- 
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
category selection
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 -->
<div id="starting_options">
  <div class="toolbar">
	  <h1>Geopaint</h1>
	  <a class="button image flip" href="#information"><img alt="<?php echo $linfo; ?>" src="/images/info.png" /></a>
	</div>
	<ul class="rounded">
		<li class="arrow"><a href="#new_project"><img src="<?php DOMAIN ?>/images/new_project.png" /><?php echo $lnewproject; ?></a></li>
		<li class="arrow"><a href="#manual"><img src="<?php DOMAIN ?>/images/manual.png" /><?php echo $lmanual; ?></a></li>
	</ul>
	<h2><?php echo $lcontinueprojects; ?></h2>
	<ul class="rounded">
		<li class="arrow"><a href="#project_selection" id="p_case_1" onClick="query_case = 1;"><img src="<?php DOMAIN ?>/images/newest_projects.png" /><?php echo $lnewestprojects; ?></a></li>
        <li class="arrow"><a href="#project_selection" id="p_case_3" onClick="query_case = 3;"><img src="<?php DOMAIN ?>/images/nearby.png" /><?php echo $lnearby; ?></a></li>
        <li class="arrow"><a href="#project_selection" id="p_case_4" onClick="query_case = 4;"><img src="<?php DOMAIN ?>/images/best_ranking.png" /><?php echo $lmostpopular; ?></a></li>
        <li class="arrow"><a href="#project_selection" id="p_case_2" onClick="query_case = 2;"><img src="<?php DOMAIN ?>/images/points.png" /><?php echo $lmostlines; ?></a></li>
	</ul>	
</div>
<!-- 
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
project selection
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 -->
 <div id="project_selection">
  <div class="toolbar">
	  <h1><?php echo $lcontinue; ?></h1>
	  <a class="cancel" href="#"><?php echo $lback; ?></a>
	</div>
    <h2 id="project_selection_h2"></h2>
    <div id="project_list"></div>
   	<a href="#" id="getMoreProjects" class="whiteButton"><?php echo $lmore; ?></a>
 </div>
<!-- 
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
create new project
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 -->
<div id="new_project">
  <div class="toolbar">
  		<h1><?php echo $lhnewproject; ?></h1>
		<a class="cancel" href="#starting_options"><?php echo $lback; ?></a>
	</div>
	<a href="#" id="createNewProject" class="whiteButton"><?php echo $lstartproject; ?></a>
	<ul class="edit rounded">
		<li><input type="text" name="pname" placeholder="<?php echo $lprojectnameopt; ?>" id="pname" /></li>
		<li><input type="password" name="ppassword" placeholder="<?php echo $lpassword; ?>" id="ppassword" /></li>
	</ul>
	<div class="info"><p><?php echo $linfopassword; ?></p></div>
</div>
<!-- 
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
draw map
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 -->
<div id="paint">
	<div class="toolbar">
	  <h1 id="project_name"></h1>
	  <a class="back" id="returnFromPainting" href="#"><?php echo $lback; ?></a>
	  <div class="toggle" style="float:right;"><input type="checkbox" onClick="recordStatus();" id="record" /></div>
	</div>
  <div class="draggable" id="gmap" style="width:320px; height:480px;"></div>
</div>
<!-- 
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
project information
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 -->
<div id="project_information">
	<div class="toolbar">
		<h1><?php echo $lprojectinfo; ?></h1>
		<a class="cancel" href="#"><?php echo $lback; ?></a>
	</div>
	<div id="project_info_content"></div>
</div>
<!-- 
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
color selection
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 -->
<div id="color">
	<div class="toolbar">
	  <h1><?php echo $lbrushcolor; ?></h1>
      <a class="cancel" href="#"><?php echo $lback; ?></a>
	</div>
	<div class="info">
	  <div id="picker"></div>
	  <input type="text" id="selectedcolor" name="selectedcolor" value="#FF0000" />
	</div>
	<a id="saveColor" href="#" class="whiteButton"><?php echo $lsave; ?></a>
</div>
<!-- 
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
pencil selection
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
-->
<div id="pencil">
	<div class="toolbar">
	  <h1><?php echo $lbrushsize; ?></h1>
      <a class="cancel" href="#"><?php echo $lback; ?></a>
	</div>
    <div class="info">
    <table cellspacing="15" cellpadding="15" id="size">
      <tr>
        <td><span style="width:4px; height:4px; -moz-border-radius:2px; -webkit-border-radius:2px;"><a href="#">4px</a></span></td>
        <td><span style="width:6px; height:6px; -moz-border-radius:3px; -webkit-border-radius:3px;"><a href="#">6px</a></span></td>
        <td><span style="width:8px; height:8px; -moz-border-radius:4px; -webkit-border-radius:4px;"><a href="#">8px</a></span></td>
      </tr>
      <tr>
        <td><span style="width:10px; height:10px; -moz-border-radius:5px; -webkit-border-radius:5px;"><a href="#">10px</a></span></td>
        <td><span style="width:12px; height:12px; -moz-border-radius:6px; -webkit-border-radius:6px;"><a href="#">12px</a></span></td>
        <td><span style="width:14px; height:14px; -moz-border-radius:7px; -webkit-border-radius:7px;"><a href="#">14px</a></span></td>
      </tr>
      <tr>
        <td><span style="width:16px; height:16px; -moz-border-radius:8px; -webkit-border-radius:8px;"><a href="#">16px</a></span></td>
        <td><span style="width:18px; height:18px; -moz-border-radius:9px; -webkit-border-radius:9px;"><a href="#">18px</a></span></td>
        <td><span style="width:20px; height:20px; -moz-border-radius:10px; -webkit-border-radius:10px;"><a href="#">20px</a></span></td>
      </tr>
    </table>
    </div>
</div>
<!-- 
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
Manual
- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
-->
<div id="manual">

	<div class="toolbar">
		<h1><?php echo $lmanual; ?></h1>
		<a class="cancel" href="#"><?php echo $lback; ?></a>
	</div>
    
	<div class="info">
        <p><?php echo $lmanual1; ?></p>
        <img src="/images/manual_map.png" />
        <p><?php echo $lmanual2; ?></p>
        <img src="/images/manual_controls.png" />
        <p><?php echo $lmanual3; ?></p>
        <img src="/images/manual_example.png" />
        <p><?php echo $lmanual4; ?></p>
        <a href="#" class="whiteButton goback"><?php echo $lokgo; ?></a>
	</div>
</div>

</body>
</html>
