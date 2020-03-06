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

				$request_url = 'http://localhost/seilo/php/api.php?restaurant=teamfood&mode=archive';
				$curl = curl_init($request_url);

				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, [
					'Content-Type: application/json'
				]);

				$response = curl_exec($curl);
				curl_close($curl);

				//echo $response;

				$obj = json_decode($response);

				console_log($obj);

				foreach ($obj as $item) {
					foreach ($item as $prop => $value) {
						echo "$prop &emsp; $value<br>";		
					}
				}
			?>

		</body>
</html>