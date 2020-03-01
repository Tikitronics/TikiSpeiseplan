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

?>
			
			