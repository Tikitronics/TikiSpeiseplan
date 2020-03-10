<!doctype HTML>
<html>
	<head>
		<title>Speiseplan Kunzmann GET_Test</title>
		<meta charset=utf-8>
		<link href="https://fonts.googleapis.com/css?family=Tangerine:700&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="../style.css">
	</head>
		<body>
			<?php
				// Klassendefinitionen hinzufügen
				require_once('classes.php');
				require_once('etc.php');

				$request_url = 'http://localhost/seilo/php/api.php?mode=archive';
				$curl = curl_init($request_url);

				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, [
					'Content-Type: application/json'
				]);

				$response = curl_exec($curl);
				curl_close($curl);

				$obj = json_decode($response);

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

				foreach($grouped_items as $day) {
					writeDay($day);
				}
			?>

			<image id="imageChef" src="..\img\luigi.png"></image>
			
			<script src="../js/tikispeiseplan.js" type="text/javascript"></script>
			<script>
				var imgChef = document.getElementById("imageChef");
				imgChef.onclick = toggleBackgroundColor;
			</script>
		</body>
</html>