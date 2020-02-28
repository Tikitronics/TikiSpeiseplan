<!doctype html>
<html>
	<head>
		<title>JSON Test</title>
	</head>
	
	<body>
		JSON Test<br>
		
		<?php
			// Takes raw data from the request
			$json = file_get_contents('php://input');

		
		
			//$jsonobj = '{"Peter":35,"Ben":37,"Joe":43}';

			$obj = json_decode($json);

			//var_dump($obj);
			
			echo $obj->message . ', ' . $obj->name;
			?>
	
	</body>
</html>