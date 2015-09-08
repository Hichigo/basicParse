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
			Array('.\\', 'cpu'),
			Array('.\\', 'video'),
			Array('.\\', 'psu'),
			Array('.\\', 'hull'),
			Array('.\\', 'motherboard'),
			Array('.\\', 'hdd'),
			Array('.\\', 'culling'),
			Array('.\\', 'ram')
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
			echo "<pre>";
			echo $q;
			echo "</pre>";
		}
		$queryes = insert_table_q($jsonAttr, $val[1]);

		foreach ($queryes as $key => $value) {
			if ($mysqli->query($value) === TRUE) {
				echo "Add to ".$val[1]." succesfully!!!<br>";
			} else {
				echo "Error creating table: " . $mysqli->error."<br>";
				echo "<pre>";
				echo $value;
				echo "</pre>";
			}
		}
		// echo '<pre style="color: #f00;">';
		// print_r($queryes);
		// echo '</pre>';

	}

	function create_table_q($arr, $tblName) {
		$q = "CREATE TABLE IF NOT EXISTS ".$tblName."(id int unique auto_increment,";

		foreach ($arr[0] as $key => $value) {
			$q .= "`".$key."`"." TEXT,\n";
		}
		$q = substr($q, 0, -2).")";
		return $q;
	}

	function insert_table_q($arr, $tblName) {
		foreach ($arr as $key => $value) {
			$q = "INSERT INTO `".$tblName."` ";
			$keys = "(";
			$vals = "VALUES(";
			foreach ($value as $k => $v) {
				$keys .= "`".$k."`,";
				$vals .= "'".$v."',";
			}
			$keys = substr($keys, 0, -1).")";
			$vals = substr($vals, 0, -1).")";
			$q .= $keys.$vals;
			$r[] = $q;
		}
		return $r;
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

