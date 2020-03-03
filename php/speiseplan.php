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
				
				// Hole die Liste der MenuItems von der Datenbank
				foreach ($pdo->query($sql) as $row) {
					$resId = $row['RestaurantId'];
					$item = new MenuItem($row['Day'], getRestaurantById($resId, $restaurants), $row['Description'], $row['Price'], $row['AdditionalDescription'], $row['FoodTypeId'], $row['PictureUrl']);
					$menu_items[] = $item;
				}

				

				/* Debug */
				console_log($menu_items);

				$grouped_items = groupMenuItemsByDate($menu_items);
				
				console_log($grouped_items);


				// @todo SCHLEIFE DURCHLAUFEN UND NACH SUB-ARRAY FÜR TAG BILDEN
				// @todo WriteDay muss array von MenuItems als Argument haben
				//writeDay($row['Day'], $row['Description'], $row['Price']);
				
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
					// @todo Logo hinzufügen
					//if(isset)
					//echo "<img src="../img/mercedes-logo.svg">
					echo '<span class="dish">' . $dish . "</span>\n";
					echo "</td>\n";
					echo '<td class="priceCell">' . $price . "€</td>\n";
					echo "</tr>\n";
					echo "</table>\n";
					echo "</div>\n\n";
				}
				
				
				
			?>
			
			<image id="imageChef" src="..\img\luigi.png"></image>

		</body>
</html>