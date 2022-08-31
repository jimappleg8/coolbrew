<?php
require_once("simpleGMapAPI.php");
require_once("simpleGMapGeocoder.php");

$map = new simpleGMapAPI();
$geo = new simpleGMapGeocoder();

$map->setWidth(600);
$map->setHeight(600);
$map->setBackgroundColor('#d0d0d0');
$map->setMapDraggable(true);
$map->setDoubleclickZoom(false);
$map->setScrollwheelZoom(true);

$map->showDefaultUI(false);
$map->showMapTypeControl(true, 'DROPDOWN_MENU');
$map->showNavigationControl(true, 'DEFAULT');
$map->showScaleControl(true);
$map->showStreetViewControl(true);

$map->setZoomLevel(14); // not really needed because showMap is called in this demo with auto zoom
$map->setInfoWindowBehaviour('SINGLE_CLOSE_ON_MAPCLICK');
$map->setInfoWindowTrigger('CLICK');

$map->addMarkerByAddress("Ravensberger Park 1 , Bielefeld", "Ravensberger Spinnerei", "Ravensberger Spinnerei", "http://google-maps-icons.googlecode.com/files/museum-historical.png");
$map->addMarkerByAddress("Universit‰tsstraﬂe 25, Bielefeld", "Universit‰t Bielefeld", "<a href=\"http://www.uni-bielefeld.de\" target=\"_blank\">http://www.uni-bielefeld.de</a>", "http://google-maps-icons.googlecode.com/files/university.png");
$map->addMarker(52.0149436, 8.5275128, "Sparrenburg Bielefeld", "Sparrenburg, 33602 Bielefeld, Deutschland<br /><img src=\"http://www.bielefeld.de/ftp/bilder/sehenswuerdigkeiten/sehenswuerdigkeiten/sparrenburg-bielefeld-435.gif\"", "http://google-maps-icons.googlecode.com/files/museum-archeological.png");

$opts = array('fillColor'=>'#0000dd', 'fillOpacity'=>0.2, 'strokeColor'=>'#000000', 'strokeOpacity'=>1, 'strokeWeight'=>2, 'clickable'=>true);
$map->addCircle(52.0149436, 8.5275128, 1500, "1,5km Umgebung um die Sparrenburg", $opts);

$opts = array('fillColor'=>'#00dd00', 'fillOpacity'=>0.2, 'strokeColor'=>'#003300', 'strokeOpacity'=>1, 'strokeWeight'=>2, 'clickable'=>true);
$map->addRectangle(52.0338, 8.487, 52.0414, 8.502, "Campus Universit‰t Bielefeld", $opts);

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
echo "<head>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
echo "<title>simpleGMapAPI test</title>";

$map->printGMapsJS();

echo "</head>\n";
echo "\n\n<body>\n\n";

// showMap with auto zoom enabled
$map->showMap(true);

echo "</body>\n";
echo "</html>\n";

?>