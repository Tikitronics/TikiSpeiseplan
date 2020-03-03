<?php

// Klassendefinitionen hinzufügen
require_once('classes.php');

/* ---------------------------------------------------------------
// Holt eine Liste der verfügbaren Retaurant von der Datenbank ab.
// --------------------------------------------------------------*/
function getRestaurantList($pdo) {
	$restaurants = array();
	
	$sql = "SELECT * FROM restaurant";
	foreach ($pdo->query($sql) as $row) {
		$restaurants[] = new Restaurant($row['RestaurantId'], $row['Name'], $row['LogoUrl']);
	}

	return $restaurants;
}

/* ---------------------------------------------------------------
// Prüft ob das übergeben Restaurant vorhanden ist und gibt das 
// Objekt zurück.
// --------------------------------------------------------------*/
function getRestaurantByName($name, $restaurants) {
	for($i = 0; $i < count($restaurants); $i++) {
		if($restaurants[$i]->name == $name) {
			return $restaurants[$i];
		}
	}
}

/* ---------------------------------------------------------------
// Prüft ob das übergeben Restaurant vorhanden ist und gibt das 
// Objekt zurück.
// --------------------------------------------------------------*/
function getRestaurantById($id, $restaurants) {
	for($i = 0; $i < count($restaurants); $i++) {
		if($restaurants[$i]->id == $id) {
			return $restaurants[$i];
		}
	}
}

/*---------------------------------------------------------------------
// Diese Funktion nimmt eine Liste von MenuItems entgegen und gibt ein 
// zweidimensionales Array zurück, bei dem die MenuItems des gleichen Tages
// gruppiert sind.
-------------------------------------------------------------------*/
function groupMenuItemsByDate($menu_items) {
	if(!isset($menu_items)) return;
	if(count($menu_items) == 1) return $menu_items;

	// Sortiere array nach datum (https://stackoverflow.com/questions/4282413/sort-array-of-objects-by-object-fields)
	usort($menu_items, function($a, $b)
	{
    	return strcmp($a->day, $b->day);
	});

	$first_date = strtotime($menu_items[0]->day);
	$last_date = strtotime(end($menu_items)->day);

	$output_array = array();
	$current_day_array = array();
	$current_day = null;

	for($i = 0; $i < count($menu_items); $i++)
	{
		if($menu_items[$i]->day == $current_day) {
			$current_day_array[] = $menu_items[$i];
		}
		else
		{
			if(count($current_day_array) != 0) $output_array[] = $current_day_array;
			$current_day_array = array();
			$current_day_array[] = $menu_items[$i];
		}

		$current_day = $menu_items[$i]->day;
	}

	$output_array[] = $current_day_array;

	return $output_array;
}

/*--------------------------------------------------
// Tag zur Anzeige konvertieren
// ------------------------------------------------*/
function convertDate($sql_date) {
	$tage = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag");
	$phpdate = strtotime( $sql_date );
	$day_german = date("d.m.Y", $phpdate);
	$day_of_the_week = date("w", $phpdate);
	return $tage[$day_of_the_week] . ', ' . $day_german;
}

/*--------------------------------------------------
// JS Logging hijacken
// ------------------------------------------------*/
function console_log( $data ){
	echo '<script>';
	echo 'console.log('. json_encode( $data ) .')';
	echo '</script>';
}

?>
			
			