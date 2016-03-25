<?php

/*
 * if ($_SERVER["REQUEST_METHOD"] == "POST"){
 *
 *
 * $text= htmlspecialchars($_POST["classes"]);
 */
// gets the scheduel that was pasted
class FutureClassesParser {
	public function parseFutureClasses($text) {
		$text = str_replace ( "'", "''", $text ); // replace ' so that sql can query
		//echo $text;
		preg_match_all ( '/(\w{2,4}\s\d{3})\s\-/', $text, $className ); // gets the semester that the class is offered in
		                                                            // pos 0 contains the year and semester extracted from the regex
		                                                            // print_r ($className);
		                                                            
		// extracts the name and title of the offered classes
		                                                            // this is a 2D array : column 0 contains both for each match, column 1 contains the class name and number, column 2 contains the classe title
		                                                            // the rows contain each different class found by the reg ex
		                                                            // print_r ($classes);
		
		$termName = array ();
		preg_match_all ( '/(\d{4})\s(Winter|Fall|Spring)/', $text, $termName );
		
		if (strpos ( $termName [0] [0], "Spring" ) !== FALSE) { // since i can't get a Regex to grab all of the sring summer
			$termName [0] [0] = $termName [0] [0] . " & Summer";
		}
		// echo $termName[0][0];
		
		// $termString = preg_split('/(\w{2,4}\s\d{3})\s\-/',$text); // splits the text into an array containing the text written in between each class
		// pos 0 is useless since it contains all the text before the first class occures
		// '/\*{2,3}.*\*{2,3}/'
		// pos 0 are the required courses and pos 1 are the electives
		
		for($i = 0; $i < count ( $className [1] ); $i ++) // loops through all the entries to find the pre-reqs for the the classes
{
			// echo count($className) . "</br>";
			// preg_match_all('/\d{4}\s(Winter|Fall|Spring & Summer)/', $termString[$i+1], $termName);
			// print_r($termName);
			echo $className [1] [$i];
			$db = mysqli_connect ( "localhost", "root", "ense400", "StudentInfo" ) or // connects to the DB
die ( "could not connect to the database: Error " . mysqli_error ( $db ) );
			
			// Checks if the class is already inserted into the DB
			$sql = "SELECT * FROM CoursesOffered WHERE courseName='" . $className [1] [$i] . "'"; // 'AND semester_offered LIKE '".$semesterOffered[0][0]."'";
			                                                                              // mysql_free_result($result);
			                                                                              // echo $sql ."<br/>";
			$result = mysqli_query ( $db, $sql );
			
			if (! $result) {
				die ( 'Query failed to execute for some reason' );
			}
			// printf ("%s (%s)\n", $row[0], $row[1]);
			
			$row = mysqli_fetch_array ( $result );
			// echo strpos($row['semester_offered'], $termName[0][0]) . "<br/>";
			// echo $row['semester_offered'];
			
			if (empty ( $row ['semester_offered'] )) {
				
				$sql = "UPDATE CoursesOffered SET semester_offered = concat( semester_offered,'" . $termName [0] [0] . "') WHERE courseName LIKE '%" . $className [1] [$i] . "%'";
				mysqli_query ( $db, $sql );
				mysqli_close ( $db );
				// echo $sql ."<br/>";
			} 

			else if (strpos ( $row ['semester_offered'], $termName [0] [0] ) !== FALSE) { // checks if the semeseter is different, if it is, then it is appended
				                                                                           // echo "hi <br/>";
			} else {
				$sql = "UPDATE CoursesOffered SET semester_offered = concat( semester_offered,', " . $termName [0] [0] . "') WHERE courseName LIKE '%" . $className [1] [$i] . "%'";
				// echo $sql;
				mysqli_query ( $db, $sql );
				mysqli_close ( $db );
			}
			
			// echo $classes[1][$i] . "<br/>";
			// echo $classes[2][$i] . "<br/>";
			// echo $pre_req[1][0];
			// echo"<br/><br/>";
		}
	}
}
echo '<a href="index.html">Home</a>';


?>