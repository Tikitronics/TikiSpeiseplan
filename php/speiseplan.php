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
				
				/* Debug */
				console_log($grouped_items);


				foreach($grouped_items as $day) {
					writeDay($day);
				}
				
				
				/*--------------------------------------------------
				// Schreibt einen Tag als HTML
				// ------------------------------------------------*/
				function writeDay($menu_day) {
					// Guard clause
					if(!isset($menu_day)) return;
					if(count($menu_day) == 0) return;

					$date_day = convertDate($menu_day[0]->day);

					// Ausgabe
					echo "<div class=\"day\">";
					echo '<h2>' . $date_day . "</h2>";
					echo "<table class=\"dishtable\">\n";

					foreach($menu_day as $dish) {
						echo "<tr>\n<td>\n";
						// @todo Logo hinzufügen
						if(isset($dish->restaurant->logoUrl))
						{
							echo '<img src="../img/' . $dish->restaurant->logoUrl . '" class="logo">';
						}
						
						echo '<span class="dish">' . $dish->descr . "</span>\n";
						echo "</td>\n";
						echo '<td class="priceCell">' . $dish->price . "€</td>\n";
						echo "</tr>\n";
					}
					
					echo "</table>\n";
					echo "</div>\n\n";
				}
				
				
				
			?>
			
			<image id="imageChef" src="..\img\luigi.png"></image>

		</body>
</html>