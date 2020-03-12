<?php
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
	// Format: siehe README.md
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

		// Prüfe übergebenes Datenformat
		if(!$obj || !is_array($obj) || empty($obj)) {
			echo 'Invalid data in POST request.';
			return;
		}

		foreach($obj AS $item) {
			if (!validateData($item)) {
				echo 'Invalid data in POST request.';
				continue;
			}

			// MySQL Verbindung herstellen
			$host = $config['host'];
			$database = $config['db'];
			$pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $config['update_user'], $item->password);

			// Hole zulässige Retaurants ab
			$restaurants = getRestaurantList($pdo);
			$restaurant = getRestaurantByName($item->restaurant, $restaurants);
			if(!isset($restaurant)) {
				echo 'Restaurant unknonwn.';
				return;
			}

			// Erstelle MenuItem Objekt
			$menu_item = createMenuItem($restaurant, $item);

			// Sende die Daten an die Datenbank
			if($item->operation == "add") {
				sendToDatabase($pdo, $menu_item);
			}
			else {
				echo "Unknown Request";
				return;
			}
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
		if(!isset($obj->price)) $dataValid = false;
		if(!isset($obj->password)) $dataValid = false;

		return $dataValid;
	}

	/* ---------------------------------------------------------------
	// Formatiert die übergebene Daten und sendet sie an die Datenbank
	// --------------------------------------------------------------*/
	function sendToDatabase($pdo, $menu_item) {
		$statement = $pdo->prepare("INSERT INTO menuitem(RestaurantId, Day, Description, AdditionalDescription, SideDish, Price) VALUES (:rid, :day, :descr, :add, :side, :price)");
		$statement->execute([
			'rid' => $menu_item->restaurant->id,
			'day' => $menu_item->day,
			'descr' => $menu_item->descr,
			'add' => $menu_item->add_descr,
			'side' => $menu_item->side,
			'price' => $menu_item->price,
		]);
	}

	/* ---------------------------------------------------------------
	// Erzeugt aus den übergebene Daten ein MenuItem-Objekt
	// --------------------------------------------------------------*/
	function createMenuItem($res, $data)
	{
		$menuItem = new MenuItem($data->day, $res, $data->description, $data->price);
		if(isset($data->additional_description)) $menuItem->add_descr = $data->additional_description;
		if(isset($data->side)) $menuItem->side = $data->side;
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
	//		today: Nur heutigen Speiseplan anzeigen
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
		if(!empty($restaurant)) $restaurant_filter = "Restaurant='" . $restaurant . "'";

		// Filterung nach Datum
		$date_filter = '';
		switch ($mode) {
			case 'archive':
				break;

			case 'nosy':
				// https://stackoverflow.com/questions/2958327/get-date-of-monday-in-current-week-in-php-4
				$thismonday = strtotime('monday this week');
				$date_filter = "Day > '" . date("Y-m-d", $thismonday) . "'";
				break;

			case 'today':
				$date_filter = "Day='" . date("Y-m-d") . "'";
				break;

			default:
				$thismonday = strtotime('monday this week');
				$thisfriday = strtotime('friday this week');
				$date_filter = "Day between '" . date("Y-m-d", $thismonday) . "' and '" . date("Y-m-d", $thisfriday) . "'";
				break;
		}

		// Anwenden der Filter falls vorhanden
		$sql_filter = [];
		if(!empty($restaurant_filter)) $sql_filter[] = $restaurant_filter;
		if(!empty($date_filter)) $sql_filter[] = $date_filter;
		if(!empty($sql_filter)) {
			$sql_filter_joined = join(" AND ", $sql_filter);
			$sql = $sql . ' WHERE ' . $sql_filter_joined;
		}

		// Ordnungskriterium
		$sql .= " ORDER BY Day, Restaurant";
		
		// MySQL Verbindung herstellen
		$host = $config['host'];
		$database = $config['db'];
		$pdo_read = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $config['user'], $config['pass']);

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