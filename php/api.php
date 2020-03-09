<?php

	/*	Erwartetes JSON Format
	{
		"password" : "****",
		"operation": "add",
		"restaurant": "Kunzmann",
		"day": "2020-03-02",
		"description" : "Leggumes",
		"additional_description" : "mit Soße",
		"price" : "4.20€"
	}
	*/

	// Klassendefinitionen hinzufügen
	require_once('classes.php');
	require_once('etc.php');
	$config = include 'config.php';
	
	// GET or POST erlaubt
	$request_type = $_SERVER['REQUEST_METHOD'];

	if($request_type == 'GET') {
		get();
	}		
	else if ($request_type == 'POST') {
		post();
	}
	else {
		echo 'Invalid request type, use GET or POST';
		return;
	}
	
	/* ---------------------------------------------------------------
	// Bearbeitung POST Request
	// Momentanes Layout:
	{
		"password" : "test",
		"operation": "add",
		"restaurant": "kunzmann",
		"day": "2020-03-09",
		"description" : "Schitzel\"Joshua\"",
		"additional_description" : "mit Soße",
		"price" : "4.80€"
	}
	----------------------------------------------------------------*/
	function post() {
		global $config;
		$json = file_get_contents('php://input');

		// Prüfe ob überhaupt Daten übergeben wurden
		if(!isset($json)) {
			echo 'No data in POST request.';
			return;
		}

		$obj = json_decode($json);

		// Prüfe übergebene Daten
		if(!$obj || !validateData($obj)) {
			echo 'Invalid data in POST request.';
			return;
		}

		// MySQL Verbindung herstellen
		$host = $config['host'];
		$database = $config['db'];
		$pdo = new PDO("mysql:host=$host;dbname=$database", $config['user'], $obj->password);

		// Hole zulässige Retaurants ab
		$restaurants = getRestaurantList($pdo);
		$restaurant = getRestaurantByName($obj->restaurant, $restaurants);
		if(!isset($restaurant)) {
			echo 'Restaurant unknonwn.';
			return;
		}

		// Erstelle MenuItem Objekt
		$menu_item = createMenuItem($restaurant, $obj);

		// Sende die Daten an die Datenbank
		if($obj->operation == "add") {
			sendToDatabase($pdo, $menu_item);
		}
		else {
			echo "Unknown Request";
			return;
		}
	}

	/* ---------------------------------------------------------------
	// Prüft die übergebene Daten, bevor diese in die Datenbank 
	// geschrieben werden.
	// --------------------------------------------------------------*/
	function validateData($obj) {
		$dataValid = true;
		$allowed_operations = array('add');

		// Prüfe ob alle notwendigen Daten vorhanden sind
		if(!isset($obj->operation)) $dataValid = false;
		if(!in_array($obj->operation, $allowed_operations)) $dataValid = false;
		if(!isset($obj->restaurant)) $dataValid = false;
		if(!isset($obj->day)) $dataValid = false;
		if(!isset($obj->description)) $dataValid = false;
		if(!isset($obj->additional_description)) $dataValid = false;
		if(!isset($obj->price)) $dataValid = false;
		if(!isset($obj->password)) $dataValid = false;

		return $dataValid;
	}

	/* ---------------------------------------------------------------
	// Formatiert die übergebene Daten und sendet sie an die Datenbank
	// --------------------------------------------------------------*/
	function sendToDatabase($pdo, $menu_item) {
		$statement = $pdo->prepare("INSERT INTO menuitem(RestaurantId, Day, Description, AdditionalDescription, Price) VALUES (:rid, :day, :descr, :add, :price)");
		$statement->execute([
			'rid' => $menu_item->restaurant->id,
			'day' => $menu_item->day,
			'descr' => $menu_item->descr,
			'add' => $menu_item->add_descr,
			'price' => $menu_item->price,
		]);
	}

	/* ---------------------------------------------------------------
	// Erzeugt aus den übergebene Daten ein MenuItem-Objekt
	// --------------------------------------------------------------*/
	function createMenuItem($res, $data)
	{
		$menuItem = new MenuItem($data->day, $res, $data->description, $data->price, $data->additional_description, null, null);
		return $menuItem;
	}

	/* ---------------------------------------------------------------
	// Bearbeitung POST Request
	// "restaurant" erlaubt, nur die Daten eines bestimmten restaurants
	//		zu zeigen. (Default: all)
	// "mode" konfiguriert die Anzeige:
	//		archive: Alle vorhandenen Daten zeigen
	//		nosy: Diese Woche und nächste anzeigen
	//		[default]: Nur diese Woche anzeigen
	----------------------------------------------------------------*/
	function get() {
		global $config; 
		$restaurant = '';
		$mode = '';
		$return_values = [];
		$sql = "SELECT * FROM menuview";

		// Prüfe Übergabeparameter und passe SQL Query an
		if(isset($_GET['restaurant'])) $restaurant = $_GET['restaurant'];
		if(isset($_GET['mode'])) $mode = $_GET['mode'];

		// Filter für Restaurant
		$restaurant_filter = '';
		if(!empty($restaurant)) $restaurant_filter = "WHERE restaurant= '" . $restaurant . "'";

		// Filterung nach Datum
		$date_filter = '';
		switch ($mode) {
			case 'archive':
				break;

			case 'nosy':
				// https://stackoverflow.com/questions/2958327/get-date-of-monday-in-current-week-in-php-4
				$thismonday = strtotime('monday this week');
				$date_filter = "WHERE Day > '" . date("Y-m-d", $thismonday) . "'";
				break;

			default:
				$thismonday = strtotime('monday this week');
				$thisfriday = strtotime('friday this week');
				$date_filter = "WHERE Day between '" . date("Y-m-d", $thismonday) . "' and '" . date("Y-m-d", $thisfriday) . "'";
				break;
		}

		// Anwenden der Filter falls vorhanden
		$sql_filter = [];
		if(!empty($restaurant_filter)) $sql_filter[] = $restaurant_filter;
		if(!empty($date_filter)) $sql_filter[] = $date_filter;
		$sql_filter_joined = join(" AND ", $sql_filter);
		$sql = $sql . ' ' . $sql_filter_joined;

		// MySQL Verbindung herstellen
		$host = $config['host'];
		$database = $config['db'];
		$pdo_read = new PDO("mysql:host=$host;dbname=$database", $config['user'], $config['pass']);

		$statement = $pdo_read->prepare($sql);
		$statement->execute();

		while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			//console_log($row);
			$return_values[] = $row;
		}

		$return_json = json_encode($return_values);

		echo $return_json;

		return;
	}

	?>