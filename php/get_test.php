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

				$request_url = 'http://localhost/seilo/php/api.php?restaurant=kunzmann&mode=archive';
				$curl = curl_init($request_url);

				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, [
					'Content-Type: application/json'
				]);

				$response = curl_exec($curl);
				curl_close($curl);

				$obj = json_decode($response);

				//console_log($obj);

				$received_items = [];

				foreach ($obj as $data) {
					// foreach ($item as $prop => $value) {
					// 	echo "$prop &emsp; $value<br>";		
					// }
					$menuItem = new MenuItem($data->Day, $data->RestaurantDisplay, $data->Description, $data->Price, $data->AdditionalDescription, null, null);
					$received_items[] = $menuItem;
				}

				$grouped_items = groupMenuItemsByDate($received_items);

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
					console.log("Ay! You clicked me!");

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