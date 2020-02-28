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

			//var_dump($obj);
			
			//echo $obj->message . ', ' . $obj->name;

			if($obj->operation == "add") {

				$statement = $pdo->prepare("INSERT INTO menuitem(RestaurantId, Day, Description, Price) VALUES (?, ?, ?, ?)");
				//$statement->execute(array('Max', 'Mustermann'));   
				//while($row = $statement->fetch()) {
   				//echo $row['vorname']." ".$row['nachname']."<br />";
   				//echo "E-Mail: ".$row['email']."<br /><br />";
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