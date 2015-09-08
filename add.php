<?php
// header('Content-type: text/html; charset=utf-8');

	$mysqli = new mysqli('localhost', 'root', '', 'system_unit');

	if ($mysqli->connect_error) {
		die('Ошибка подключения (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
	} else {
		echo "MySQL ok!";
	}


	echo !in_array("needle", Array()).'<br><br>';
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
		$r = Array();
		for($i = 2; $i < $nFiles; $i++) {
			$f = fopen($path.'\\'.$files[$i], 'rb');
			$jsonAttr = json_decode(fread($f, filesize($path.'\\'.$files[$i])));
			fclose($f);
			

			// выбираем все возможные имена колонок для каждой таблицы
			foreach ($jsonAttr as $jsk => $jsv) { // где то ошибка КАВЫЧКИ
				foreach ($jsv as $kjs => $vjs) {
					if(!in_array($kjs, $r)) {
						$r[] = $kjs;
					}
				}
			}
			// echo $val[1]."<pre>";
			// print_r($r);
			// echo "</pre>";
		}
		$q = create_table_q($r, $val[1], $mysqli);
		
		if ($mysqli->query($q) === TRUE) {
			echo "Table MyGuests created successfully<br>";
		} else {
			echo "Error creating table: " . $mysqli->error."<br>";
			echo "<pre>";
			echo $q;
			echo "</pre>";
		}

		$queryes = insert_table_q($jsonAttr, $val[1], $mysqli); // запросы для добавления в бд

		foreach ($queryes as $key => $value) { //добавление в бд
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

	function create_table_q($arr, $tblName, $msql) {
		$q = "CREATE TABLE IF NOT EXISTS ".$tblName."(id int unique auto_increment,"; //

		foreach ($arr as $key => $value) {
			$q .= "`".$value."`"." TEXT,";
		}
		$q = substr($q, 0, -1).")";
		return $q;
	}

	function insert_table_q($arr, $tblName, $msql) {
		foreach ($arr as $key => $value) {
			$q = "INSERT INTO `".$tblName."` ";
			$keys = "(";
			$vals = "VALUES(";
			foreach ($value as $k => $v) {
				$keys .= "`".$k."`,";
				$vals .= "'".$msql->real_escape_string($v)."',";
			}
			$keys = substr($keys, 0, -1).")";
			$vals = substr($vals, 0, -1).")";
			$q .= $keys.$vals;
			$r[] = $q;
		}
		return $r;
	}

	function check_max_attr($arr) { // переписать для сбора всех возможных полей
		$r = Array();
		foreach ($arr as $k => $v) {
			foreach ($v as $key => $val) {
				echo !in_array($key, $r);
				if(!in_array($key, $r)) {
					$r[] = $key;
				}
			}
		}
		return $r;
	}