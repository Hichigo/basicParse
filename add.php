<?php
// header('Content-type: text/html; charset=utf-8');

	$mysqli = new mysqli('localhost', 'root', '', 'system_unit');

	if ($mysqli->connect_error) {
		die('Ошибка подключения (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
	} else {
		echo "MySQL ok!";
	}

	// $q = "CREATE TABLE Persons(PersonID int unique auto_increment, LastName varchar(255),FirstName varchar(255),Address varchar(255),City varchar(255))";

	// if ($mysqli->query($q) === TRUE) {
	// 	echo "Table MyGuests created successfully";
	// } else {
	// 	echo "Error creating table: " . $mysqli->error;
	// }

	$dataPath = Array(
			Array('C:\\OpenServer\\domains\parse.my\\', 'cpu'),
			Array('C:\\OpenServer\\domains\parse.my\\', 'video'),
			Array('C:\\OpenServer\\domains\parse.my\\', 'psu'),
			Array('C:\\OpenServer\\domains\parse.my\\', 'hull'),
			Array('C:\\OpenServer\\domains\parse.my\\', 'motherboard'),
			Array('C:\\OpenServer\\domains\parse.my\\', 'hdd'),
			Array('C:\\OpenServer\\domains\parse.my\\', 'culling'),
			Array('C:\\OpenServer\\domains\parse.my\\', 'ram')
		);

	foreach ($dataPath as $dataK => $val) {
	
		$path = $val[0].$val[1];
		$files = scandir($path);

		$nFiles = count($files);
		$max = 0;

		$queryes = Array();

		for($i = 2; $i < $nFiles; $i++) {
			$f = fopen($path.'\\'.$files[$i], 'rb');
			$jsonAttr = json_decode(fread($f, filesize($path.'\\'.$files[$i])));
			fclose($f);
			$t = check_max_attr($jsonAttr);
			if ($t > $max) {
				$max = $t;
			}

			
			foreach ($jsonAttr as $key => $value) { //составляем запросы
				$q = "INSERT INTO `".$val[1]."` ";
				$col = "(";
				$colVal = "VALUES (";
				foreach ($value as $k => $v) {
					$col .= $k.",";
					$colVal .= "'".$v."',";
				}
				$col = substr($col, 0, -1).")";
				$colVal = substr($colVal, 0, -1).")";
				$q .= $col.$colVal;
				$queryes[] = $q;
			}
		}
		$q = create_table_q($max, $val[1]);

		if ($mysqli->query($q) === TRUE) {
			echo "Table MyGuests created successfully<br>";
		} else {
			echo "Error creating table: " . $mysqli->error."<br>";
			echo $q."<br><br>";
		}
		// echo '<pre style="color: #f00;">';
		// print_r($max[1]);
		// echo '</pre>';
	}

	function create_table_q($arr, $tblName) {
		$q = "CREATE TABLE IF NOT EXISTS ".$tblName."(id int unique auto_increment,";

		foreach ($arr[0] as $key => $value) { // fix this xD
			$k = str_replace(" ", "_", $key);
			$k = str_replace("-", "_", $k);
			$k = str_replace("(", "_", $k);
			$k = str_replace(")", "", $k);
			$k = str_replace("+", "", $k);
			$k = str_replace(".", "_", $k);
			$k = str_replace(",", "_", $k);
			$k = str_replace("/", "_", $k);
			$k = str_replace("\\", "_", $k);
			$k = str_replace("\"", "_", $k);
			$q .= $k." TEXT,";
		}
		$q = substr($q, 0, -1).")";
		return $q;
	}

	function insert_table_q($arr) {

	}

	function check_max_attr($arr) {
		$max = 0;
		foreach ($arr as $k => $v) {
			$n = count((array)$v);
			if($n > $max) {
				$r[0] = $v;
				$r[1] = $n;
			}
		}
		return $r;
	}

