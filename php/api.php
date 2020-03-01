<!doctype html>
<html>
	<head>
		<title>TikiSpeiseplan API</title>
	</head>
	
	<body>
		<?php
			// Klassendefinitionen hinzufügen
			require 'classes.php';

			// Daten von Request entgegennehmen
			$json = file_get_contents('php://input');

			// Prüfe ob überhaupt Daten übergeben wurden
			if(!isset($json)) errorMessage();

			// MySQL Verbindung herstellen
			$pdo = new PDO('mysql:host=localhost;dbname=food', 'test', 'test');

			// Hole zulässige Retaurants ab
			$restaurants = getValidRestaurants($pdo);

			$obj = json_decode($json);

			// Prüfe JSON Format
			var_dump($obj);
			if(!isset($obj->operation)) {
				errorMessage();
				return;
			}

			//echo $obj->message . ', ' . $obj->name;

			if($obj->operation == "add") {

				$statement = $pdo->prepare("INSERT INTO menuitem(RestaurantId, Day, Description, Price) VALUES (:rid, :day, :descr, :price)");
				$statement->execute([
					'rid' => $obj->restaurantid,
					'day' => $obj->day,
					'descr' => $obj->description,
					'price' => $obj->price,
				]);
				   
				//$neue_id = $pdo->lastInsertId();
				//echo "Neues Gericht mit id $neue_id angelegt";
			}
			else {
				echo "Unknown Request";
				return;
			}

			// Fehlerausgabe
			function errorMessage() {
				echo "<b>Fehler!</b>";
				return;
			}

			// Prüft die übergebene DAten, bevor diese in die Datenbank geschrieben werden
			function validateData() {

			}

			function getValidRestaurants($pdo) {
				// Restaurants holen
				$restaurants = array();
				
				$sql = "SELECT * FROM restaurant";
				foreach ($pdo->query($sql) as $row) {
					$restaurants[$row['RestaurantId']] = new Restaurant($row['Name'], $row['LogoUrl']);
				}

				return $restaurants;
			}

			?>
	
	</body>
</html>