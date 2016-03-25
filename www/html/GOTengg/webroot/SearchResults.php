<?php

if ((include '/var/www/html/GOTengg/src/Model.php') == TRUE) {}

	if ($_SERVER["REQUEST_METHOD"] == "POST"){
		

		// set instance of the db
		$connection = new dbConnection ();
		$connection->connect ();
		$db = $connection->getDb ();
		
		$search= htmlspecialchars($_POST['search']);
		$search = htmlentities($search);
		
		// this will add a space if one is missing inbetween the course letters and numbers
		// if a space already exists then nothing will be changed
		preg_match('/(\w{1,4})\s?(\d{0,3})/', $search, $Sarray);
		
		if ($Sarray[2] !=""){
		$search= $Sarray[1] . " " . $Sarray[2];
		}
		else {
			$search=$Sarray[1];
		}
		
		$sql = "SELECT * FROM CoursesOffered WHERE courseName LIKE '%". $search ."%' LIMIT 20";   //search the DB for matches
		mysqli_free_result($result);
		$result = mysqli_query($db,$sql);

		if($result){//If query successfull
	
			echo	"	<thead>
							<tr class = 'results'>
								<th>Course Name</th>
								<th>Course Title</th>
								<th>Prerequisites</th>
								<th>Semester Offered</th>
						
						</thead>
						<tbody class='connectedSortable'>";
			
		while ($row = mysqli_fetch_assoc($result)) { //Creates a loop to loop through results
			
			echo "<tr class='results'><td>" . $row ['courseName'] . "</td><td>" . $row ['courseTitle'] . "</td>" . "<td>" . $row ['pre_reqs'] . "</td>" . "<td>" . $row ['semester_offered'] . "</td></tr>"; // $row['index'] the index here is a field name
			}
		}
		else{
			echo "No Results Found";
		}
		
	}


?>