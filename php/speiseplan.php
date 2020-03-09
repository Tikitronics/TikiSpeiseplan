<!doctype HTML>
<html>
	<head>
		<title>Speiseplan Kunzmann</title>
		<meta charset=utf-8>
		<link href="https://fonts.googleapis.com/css?family=Tangerine:700&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="../style.css">
	</head>
		<body>

			<?php
				// Klassendefinitionen hinzufügen
				require_once('classes.php');
				require_once('etc.php');
				$config = include 'config.php';
				
				// MySQL Verbindung herstellen
				$host = $config['host'];
				$database = $config['db'];
				$pdo = new PDO("mysql:host=$host;dbname=$database", $config['user'], $config['pass']);
				
				// Restaurants holen
				$restaurants = getRestaurantList($pdo);
				
				// Speisekarte holen
				$menu_items = array();
				
				//$statement = $pdo->prepare("SELECT * FROM menuItem WHERE Day = :tag ORDER BY Day ASC");
				$sql = "SELECT * FROM menuItem ORDER BY Day ASC";
				
				// Hole die Liste der MenuItems von der Datenbank
				foreach ($pdo->query($sql) as $row) {
					$resId = $row['RestaurantId'];
					$item = new MenuItem($row['Day'], getRestaurantById($resId, $restaurants), $row['Description'], $row['Price']);
					$menu_items[] = $item;
				}

				$grouped_items = groupMenuItemsByDate($menu_items);
				
				foreach($grouped_items as $day) {
					writeDay($day);
				}
				
			?>
			
			<image id="imageChef" src="..\img\luigi.png"></image>
			
			<script>
				var imgChef = document.getElementById("imageChef");
				imgChef.onclick = toggleBackgroundColor;

				// ---------------------------------------------------------
				// Ändert die Farbe des Hintergrundmusters
				// ---------------------------------------------------------
				function toggleBackgroundColor() {
					//console.log("Ay! You clicked me!");

					let style = window.getComputedStyle(document.body);
					if(style)
					{
						let current = style.getPropertyValue('background-image');
						if(current.includes("rot")) {
							document.body.style.backgroundImage = "url('../img/karo_blau.png')";
						}
						else {
							document.body.style.backgroundImage = "url('../img/karo_rot.png')";
						}
					}
					console.log(style.getPropertyValue('background-image'));
				}
			</script>

		</body>
</html>