<!doctype HTML>
<html>
	<head>
		<title>Inoffizieller INRO Speiseplan</title>
		<meta charset=utf-8>
		<link href="https://fonts.googleapis.com/css?family=Tangerine:700&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Raleway:400i&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="style.css">
	</head>
		<body>
			<?php
				// Klassendefinitionen hinzufügen
				require_once('php/classes.php');
				require_once('php/etc.php');
				$config = include 'php/config.php';
				$api_url = $config['api_url'];

				// GET Parameter prüfen (Bedeutung siehe api.php)
				$get_params = [];				
				if(isset($_GET['restaurant'])) {
					$get_params['restaurant'] = $_GET['restaurant'];
				}
				if(isset($_GET['mode'])) {
					$get_params['mode'] = $_GET['mode'];
				}
				if(!empty($get_params)) {
					$api_url .= '?' . http_build_query($get_params);
				}
				
				$curl = curl_init($api_url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, [
					'Content-Type: application/json'
				]);

				$response = curl_exec($curl);
				curl_close($curl);

				$obj = json_decode($response);

				if(isset($obj) && !empty($obj)) {
					$received_items = [];

					foreach ($obj as $data) {
						$menuItem = new MenuItem($data->Day, $data->RestaurantDisplay, $data->Description, $data->Price);
						$menuItem->id = $data->Id;
						if(isset($data->AdditionalDescription)) $menuItem->add_descr = $data->AdditionalDescription;
						if(isset($data->SideDish)) $menuItem->side = $data->SideDish;
						if(isset($data->RestaurantLogo)) $menuItem->restaurant_logo = $data->RestaurantLogo;
						$received_items[] = $menuItem;
					}

					$grouped_items = groupMenuItemsByDate($received_items);

					if(isset($grouped_items) && !empty($grouped_items)) {
						//echo "<div id=\"menu\">\n";
						foreach($grouped_items as $day) {
							writeDay($day);
						}
						//echo '</div>';
					}
					else {
						echo '<h2>porca miseria, kein Speiseplan gefunden!</h2>';
					}
				}
				else {
					echo '<h2>porca miseria, kein Speiseplan gefunden!</h2>';
				}
			?>

			<image id="imageChef" src="img\luigi.png"></image>
			
			<script src="js/tikispeiseplan.js" type="text/javascript"></script>
			<script>
				var imgChef = document.getElementById("imageChef");
				imgChef.onclick = toggleBackgroundColor;
			</script>
		</body>
</html>