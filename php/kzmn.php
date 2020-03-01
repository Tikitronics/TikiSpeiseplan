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
					console_log($resId);
					$item = new MenuItem($row['Day'], getRestaurantById($resId, $restaurants), $row['Description'], $row['Price'], $row['AdditionalDescription'], $row['FoodTypeId'], $row['PictureUrl']);
					
					$menu_items[] = $item;
					
					writeDay($row['Day'], $row['Description'], $row['Price']);
				}
				
				console_log($menu_items);
				
				function writeDay($day, $dish, $price) {
					
					// Prüfungen hier

					
					// Ausgabe
					echo "<div class=\"day\">";
					echo '<h1>' . convertDate($day) . '</h1>';
					echo "<table class=\"dishtable\">";
					echo "<tr>";
					echo "<td>";
					echo '<span class="dish">' . $dish . '</span>';
					echo "</td>";
					echo '<td>' . $price . '€</td>';
					echo "</tr>";
					echo "</table>";
					echo "</div>";
				}
				
				function convertDate($sql_date) {
					$tage = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag");
					$phpdate = strtotime( $sql_date );
					$day_german = date("d.m.Y", $phpdate);
					$day_of_the_week = date("w", $phpdate);
					return $tage[$day_of_the_week] . ', ' . $day_german;
				}
				
				// Log to Browser Console
				function console_log( $data ){
					echo '<script>';
					echo 'console.log('. json_encode( $data ) .')';
					echo '</script>';
				}
				
			?>
			
			<div class="day">
				<h1>Mittwoch, 12.02.2020</h1>
				<table class="dishtable">
					<tr>
						<td>
							<span class="dish">Hähnchenbrust "Melba" mit Curry — Cocossoße dazu Reis</span>
						</td>
						<td>
							4,80€
						</td>
					</tr>
					<tr>
						<td>
							<span class="dish">Käsespätzle mit Speckwürfel</span>
						</td>
						<td>
							4,80€
						</td>
					</tr>
				</table>
			</div>
		
			
			<div id="imageChef">
				<image src="luigi.png"></image>
			</div>
		</body>
</html>