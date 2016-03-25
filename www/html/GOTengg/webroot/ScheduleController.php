<?php
if ((include '/var/www/html/GOTengg/src/Model.php') == TRUE) {
}

switch ($_POST ["functionname"]) {
	
	case 'listSchedules' :
		listSchedules ();
		break;
	case 'printSchedule' :
		printSchedule ();
		break;
	/*default :
		printSchedule ();
		break;*/
}


/*
 * This function will print the selescted schedule
 */
function printSchedule() {
	if ($_SERVER ["REQUEST_METHOD"] == "POST") {
		
		// set instance of the db
		$connection = new dbConnection ();
		$connection->connect ();
		$db = $connection->getDb ();
		
		// $db = mysqli_connect("localhost", "root", "ense400", "StudentInfo") or die("could not connect to the database: Error " . mysqli_error($db));
		
		$search = htmlspecialchars ( $_POST ["schedule"] );
		$search = htmlentities ( $search );
		
		// this will add a space if one is missing inbetween the course letters and numbers
		// if a space already exists then nothing will be changed
		/*
		 * preg_match ( '/(\w{1,4})\s?(\d{0,3})/', $search, $Sarray );
		 *
		 * if ($Sarray [2] != "") {
		 * $search = $Sarray [1] . " " . $Sarray [2];
		 * } else {
		 * $search = $Sarray [1];
		 * }
		 */
		
		$sql = "SELECT * FROM Schedule WHERE id LIKE '%" . $search . "%' LIMIT 20"; // search the DB for matches
		mysqli_free_result ( $result );
		$result = mysqli_query ( $db, $sql );
		
		if ($result) { // If query successfull
			
			echo "	<thead>
							<tr>
								<th>Program</th>

							</tr>
							<tr></tr>
						</thead>
						<tbody>";
			
			while ( $row = mysqli_fetch_assoc ( $result ) ) { // Creates a loop to loop through results
				
				echo "<tr><td>" . $row ['program'] . "</td></tr>";
				echo "<tr><td>" . $row ['Sem1_name'] . " " . $row ['semester1'] . "</td></tr>";
				echo "<tr><td>" . $row ['Sem2_name'] . " " . $row ['semester2'] . "</td></tr>";
				echo "<tr><td>" . $row ['Sem3_name'] . " " . $row ['semester3'] . "</td></tr>";
				echo "<tr><td>" . $row ['Sem4_name'] . " " . $row ['semester4'] . "</td></tr>";
				echo "<tr><td>" . $row ['Sem5_name'] . " " . $row ['semester5'] . "</td></tr>";
				echo "<tr><td>" . $row ['Sem6_name'] . " " . $row ['semester6'] . "</td></tr>";
				echo "<tr><td>" . $row ['Sem7_name'] . " " . $row ['semester7'] . "</td></tr>";
				echo "<tr><td>" . $row ['Sem8_name'] . " " . $row ['semester8'] . "</td></tr>";
				echo "<tr><td>" . $row ['Sem9_name'] . " " . $row ['semester9'] . "</td></tr>";
				echo "<tr><td>" . $row ['electives'] . "</td></tr>";
				echo "<tbody>";
			}
		} else {
			echo "No Results Found";
		}
	}
}

/**
 * ****************************************************
 * This function will query the db to get the names of all the schedules that are currently in the db
 * It will return an array containing a list of availible schedules in the DB
 */
function listSchedules() {
	
	// set instance of the db
	$connection = new dbConnection ();
	$connection->connect ();
	$db = $connection->getDb ();
	
	$schduleList = array ();
	
	$sql = "SELECT * FROM Schedule";
	$result = mysqli_query ( $db, $sql );
	
	// loops through all the queries
	$i = 0;
	while ( $row = mysqli_fetch_assoc ( $result ) ) {
		
		$schduleList [$i] = $row ['year'] . " " . $row ['program'];
		// echo $schduleList [$i] . "</br>";
		echo "<option value='" . $row ['id'] . "'>" . $schduleList [$i] . "</option>";
		$i ++;
	}
	
	return;
	// return $scheduleList;
}

?>