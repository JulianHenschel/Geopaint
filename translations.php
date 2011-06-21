<?php
if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	$nolanguage = "Your locale could not be detected. I’ll show you the site in English. :)";
	$lang = "en";
}
else $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);

if($lang == "en") {
	$lloading = "Loading &hellip;";
	$lback = "Back";

	$linfo = "Info";
	$lby = "Created by";
	$lcontact = "Contact us";
	$lthanks = "Thanks to";
	$lthanksmentoring = "For mentoring and support.";
	$lthankstesting = "For testing and his iPhone. ;-)";
	$lthanksgeolocation = "For the basic geolocation code.";
	$lthanksbiggestdrawing = "For his (hoax) Biggest Drawing in the World.";
	$lthanksberlinartists = "Berlin artists";
	$lthanksberlin = "For some real-life Geopaint. :-)";
	$llocation = "Greetings to";

	$lmanual = "How to";
	$lnewestprojects = "Newest projects";
	$lnearby = "Nearby your location";
	$lmostpopular = "Most viewed";
	$lmostlines = "Most lines";
	$lcontinue = "Continue";
	$lmore = "More &hellip;";
	$lprojectinfo = "Information";
	$lbrushcolor = "Brush color";
	$lsave = "Save";
	$lbrushsize = "Brush size";

	$linfoloading		= "Trying to get location information &ndash; if the browser asks for confirmation, please accept.";
	if(!isset($_SERVER['HTTP_USER_AGENT'])) {
		if(strpos($_SERVER['HTTP_USER_AGENT'],"iPhone")) $linfoloading = "Location information is transferred. For best accuracy, enable location services in Settings &rarr; General: Location services. For additional accuracy, be sure to have 3G enabled in Settings &rarr; General &rarr; Network: Enable 3G.";
		if(strpos($_SERVER['HTTP_USER_AGENT'],"Android")) $linfoloading = "Location information is transferred. For best accuracy, enable location services in Settings &rarr; Security & location: use wireless networks & Enable GPS satellites. For additional accuracy, be sure to have 3G enabled in Settings &rarr; Wireless &rarr; Mobile networks: use only 2G networks (switch off if on).";
	}
	//Transfer does not continue in sleep mode, so:
	//Settings &rarr; Sound & display &rarr; Screen timeout: Never timeout.
	//Settings &rarr; General &rarr; Auto-Lock: Never.

	$lcompatibility		= "Your browser does not seem to have support for geolocation, probably because it is outdated. Check out Chrome, Firefox or Safari. There should be no problems with the standard browser on Android devices and the iPhone.";

	$lprojectname		= "Project name";
	$lprojectnameopt	= "Project name (optional)";
	$lnewproject		= "Start new project";
	$lhnewproject		= "New project";
	$lcontinueprojects	= "Continue projects";

	$lstartproject		= "Paint!";
	$lpassword			= "Password (optional)";
	$linfopassword		= "All the info is optional. Password protected projects can be viewed by anyone but not edited. Unused and empty projects will be deleted after 24 hours.";
	//You automatically get a cryptic address for your project, so you can publish it or only share it to friends and collaborators.

	$lrecord			= "Paint";
	$lbrush				= "Brush";
	$lcolor				= "Color";
	
	$lmanual1 = "Geopaint is a Google Maps mashup with the map as canvas and your phone as brush. The website will periodically fetch your current location and connect the dots to line paintings.";
	$lmanual2 = "You can use it with iPhone, Android-Phones and every other device whose browser supports geolocation. Just flick the switch to start and move around to draw with your location.";
	$lmanual3 = "You have to make sure that your phone does not go into sleep mode (set the display timeout to »never«). Otherwise, the connection will be interrupted and no points set.";
	$lmanual4 = "It is also useful to set a password for your project. Then it can only be edited with the password and you can also delete it if you want. Projects without password can not be deleted.";
	$lokgo = "Ok, let’s go!";
}
elseif($lang == "de") {
	$lloading			= "L&auml;dt &hellip;";
	$lback				= "Zur&uuml;ck";
	
	$linfo = "Info";
	$lby = "Entwickelt von";
	$lcontact = "Schreibt uns";
	$lthanks = "Danke an";
	$lthanksmentoring = "F&uuml;r die Betreuung und Unterst&uuml;tzung.";
	$lthankstesting = "F&uuml;r’s Testen und sein iPhone. ;)";
	$lthanksgeolocation = "F&uuml;r die Basis der Standortbestimmung.";
	$lthanksbiggestdrawing = "F&uuml;r seine (geschwindelte) Gr&ouml;&szlig;te Zeichnung der Welt.";
	$lthanksbiggestdrawing = "Für seine (falsche) Größte Zeichnung der Welt.";
	$lthanksberlinartists = "Berliner Künstler";
	$lthanksberlin = "F&uuml;r reales Geopaint. :)";
	$llocation = "Grüße nach";
	
	$lmanual        = "Anleitung";
	$lnewestprojects = "Neueste Projekte";
	$lnearby = "In deiner N&auml;he";
	$lmostpopular = "Am meisten angeschaut";
	$lmostlines = "Mit den meisten Linien";
	$lcontinue = "Weiterf&uuml;hren";
	$lmore = "Mehr &hellip;";
	$lprojectinfo = "Informationen";
	$lbrushcolor = "Strichfarbe";
	$lsave = "Speichern";
	$lbrushsize = "Strichgr&ouml;&szlig;e";

	$linfoloading		= "Standortinformationen werden gesucht &ndash; bitte akzeptiere, wenn der Browser daf&uuml;r eine Best&auml;tigung braucht.";
	if(!isset($_SERVER['HTTP_USER_AGENT'])) {
		if(strpos($_SERVER['HTTP_USER_AGENT'],"iPhone")) $linfoloading = "Standortinformationen werden &uuml;bertragen. Aktiviere am besten die Standort-Einstellungen in Einstellungen &rarr; Allgemein: Ortungsdienste. F&uuml;r noch mehr Genauigkeit, aktiviere 3G-Netzwerke in Einstellungen &rarr; Allgemein &rarr; Netzwerk: 3G aktivieren.";
		if(strpos($_SERVER['HTTP_USER_AGENT'],"Android")) $linfoloading = "Standortinformationen werden &uuml;bertragen. Aktiviere am besten die Standort-Einstellungen in Einstellungen &rarr; Sicherheit und Standort: Wireless nutzen & GPS-Satelliten aktivieren. F&uuml;r noch mehr Genauigkeit, aktiviere 3G-Netzwerke in Einstellungen &rarr; Wireless &rarr; Mobile Netzwerke: Nur 2G-Netzwerke (Haken entfernen).";
	}
	//Übertragung rei&szlig;t im Schlafmodus ab, daher:
	//Einstellungen &rarr; Sound & Display &rarr; Display-Timeout: Kein Timeout.
	//Einstellungen &rarr; Allgemein &rarr; Automatische Sperre: Nie.

	$lcompatibility		= "Dein Browser unterst&uuml;tzt anscheinend keine Standortsbestimmung, wahrscheinlich weil er veraltet ist. Probier mal Chrome, Firefox oder Safari. Auf Android-Handys und dem iPhone sollte es mit den Standard-Browsern keine Probleme geben.";

	$lprojectname		= "Projektname";
	$lprojectnameopt	= "Projektname (optional)";
	$lnewproject		= "Neues Projekt";
	$lhnewproject		= "Neues Projekt";
	$lcontinueprojects	= "Projekte weiterf&uuml;hren";

	$lstartproject		= "Malen!";
	$lpassword			= "Passwort (optional)";
	$linfopassword		= "Durch Passwort geschützte Projekte können zwar von jedem eingesehen, aber nicht geändert werden. Ungenutzte und leere Projekte werden nach 24 Stunden gel&ouml;scht.";
	//Du bekommst f&uuml;r dein Projekt automatisch eine kryptische Adresse zugewiesen, damit du es ver&ouml;ffentlichen oder mit Freunden und Mitarbeitern teilen kannst.

	$lrecord			= "Malen";
	$lbrush				= "Pinsel";
	$lcolor				= "Farbe";
	
	$lmanual1 = "Geopaint ist ein Google Maps-Mashup mit der Karte als Leinwand und dem Handy als Pinsel. Dazu ruft die Webseite regelmäßig deinen aktuellen Standort ab und verbindet die Punkte zu Linienzeichnungen.";
	$lmanual2 = "Es ist nutzbar mit iPhone, Android-Handy und jedem anderen Gerät, dessen Browser Standortbestimmung unterstützt. Leg den Schalter um und beweg dich, um mit deinem Standort zu malen.";
	$lmanual3 = " Beachte dabei, dass dein Handy nicht in den Ruhezustand schaltet, während du zeichnest &ndash; sonst wird die Übertragung unterbrochen. Deaktiviere dafür die automatische Bildschirm-Sperre.";
	$lmanual4 = "Es ist außerdem sinnvoll, deinem Projekt ein Passwort zu geben. Dann ist das Projekt nur mit Passwort veränderbar und du hast auch die Möglichkeit, es wieder zu löschen. Projekte ohne Passwort können nicht gelöscht werden.";
	$lokgo = "Ok, verstanden!";
}
?>
