<!doctype html>
<html>
	<head>
		<title>TikiSpeiseplan API</title>
	</head>
	
	<body>
		<?php
			// Klassendefinitionen hinzufügen
			require_once('classes.php');
			require_once('etc.php');

			// Daten von Request entgegennehmen
			$json = file_get_contents('php://input');

			// Prüfe ob überhaupt Daten übergeben wurden
			if(!isset($json)) errorMessage();

			// Input Daten interpretieren
			$obj = json_decode($json);

			// Prüfe übergebene Daten
			if(!validateData($obj)) {
				errorMessage();
				return;
			}

			// MySQL Verbindung herstellen
			$pdo = new PDO('mysql:host=localhost;dbname=food', 'test', $obj->password);

			// Hole zulässige Retaurants ab
			$restaurants = getRestaurantList($pdo);
			$restaurant = getRestaurantByName($obj->restaurant, $restaurants);
			if(!isset($restaurant)) {
				errorMessage();
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


			/* ---------------------------------------------------------------
			// Fehlerausgabe
			// --------------------------------------------------------------*/
			function errorMessage() {
				echo "<b>Fehler!</b>";
				return;
			}

			/* ---------------------------------------------------------------
			// Prüft die übergebene Daten, bevor diese in die Datenbank 
			// geschrieben werden.
			// --------------------------------------------------------------*/
			function validateData($obj) {
				$dataValid = true;

				// Prüfe ob alle notwendigen Daten vorhanden sind
				if(!isset($obj->operation)) $dataValid = false;
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

			?>
	
	</body>
</html>