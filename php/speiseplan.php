<!doctype HTML>
<html>
	<head>
		<title>Speiseplan Kunzmann</title>
		<meta charset=utf-8>
		<link href="https://fonts.googleapis.com/css?family=Tangerine:700&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="../style.css">
	</head>
		<body>

			<?php
				// Klassendefinitionen hinzufügen
				require_once('classes.php');
				require_once('etc.php');
				
				// MySQL Verbindung herstellen
				$pdo = new PDO('mysql:host=localhost;dbname=food', 'test', 'test');
				
				// Restaurants holen
				$restaurants = getRestaurantList($pdo);
				
				// Speisekarte holen
				$menu_items = array();
				
				//$statement = $pdo->prepare("SELECT * FROM menuItem WHERE Day = :tag ORDER BY Day ASC");
				$sql = "SELECT * FROM menuItem ORDER BY Day ASC";
				
				foreach ($pdo->query($sql) as $row) {
					$resId = $row['RestaurantId'];
					$item = new MenuItem($row['Day'], getRestaurantById($resId, $restaurants), $row['Description'], $row['Price'], $row['AdditionalDescription'], $row['FoodTypeId'], $row['PictureUrl']);
					
					$menu_items[] = $item;
					
					writeDay($row['Day'], $row['Description'], $row['Price']);
				}
				
				console_log($menu_items);
				

				/*--------------------------------------------------
				// Schreibt einen Tag als HTML
				// ------------------------------------------------*/
				function writeDay($day, $dish, $price) {
					
					// Prüfungen hier

					
					// Ausgabe
					echo "<div class=\"day\">";
					echo '<h2>' . convertDate($day) . "</h2>";
					echo "<table class=\"dishtable\">\n";
					echo "<tr>\n";
					echo "<td>\n";
					echo '<span class="dish">' . $dish . "</span>\n";
					echo "</td>\n";
					echo '<td class="priceCell">' . $price . "€</td>\n";
					echo "</tr>\n";
					echo "</table>\n";
					echo "</div>\n\n";
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
			
			<image id="imageChef" src="..\img\luigi.png"></image>

		</body>
</html>