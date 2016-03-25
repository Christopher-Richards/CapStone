<?php

/*
 * if ($_SERVER["REQUEST_METHOD"] == "POST"){
 *
 *
 * $text= htmlspecialchars($_POST["classes"]);
 */
// gets the scheduel that was pasted
class ParseOfferedClasses {
	public function parseOfferedClass($text) {
		$text = str_replace ( "'", "''", $text ); // replace ' so that sql can query
		
		$semesterOffered = "";
		preg_match_all ( '/\d{4}\s(Winter|Fall|Spring & Summer|Spring\/Summer)/', $text, $semesterOffered ); // gets the semester that the class is offered in
		                                                                                                 // pos 0 contains the year and semester extracted from the regex
		                                                                                                 
		// print_r ($semesterOffered);
		                                                                                                 // echo "<br/>";
		                                                                                                 
		// extracts the name and title of the offered classes
		                                                                                                 // this is a 2D array : column 0 contains both for each match, column 1 contains the class name and number, column 2 contains the classe title
		                                                                                                 // the rows contain each different class found by the reg ex
		$classes = array ();
		preg_match_all ( '/(\w{2,4}\s\d{3})\s\-(.*)/', $text, $classes );
		// print_r ($classes);
		
		$pre_req_string = preg_split ( '/(\w{2,4}\s\d{3})\s\-(.*)/', $text ); // splits the text into an array containing the text written in between each class
		                                                                  // pos 0 is useless since it contains all the text before the first class occures
		                                                                  // '/\*{2,3}.*\*{2,3}/'
		                                                                  // pos 0 are the required courses and pos 1 are the electives
		
		for($i = 0; $i < count ( $pre_req_string ) - 1; $i ++) // loops through all the entries to find the pre-reqs for the the classes
{
			
			preg_match_all ( '/\*{2,3}\s?Prerequisite:\s?(.*)\*{3}/', $pre_req_string [$i + 1], $pre_req );
			
			$db = mysqli_connect ( "localhost", "root", "ense400", "StudentInfo" ) or // connects to the DB
die ( "could not connect to the database: Error " . mysqli_error ( $db ) );
			
			// Checks if the class is already inserted into the DB
			$sql = "SELECT * FROM CoursesOffered WHERE courseName='" . $classes [1] [$i] . "'"; // 'AND semester_offered LIKE '".$semesterOffered[0][0]."'";
			                                                                            // echo $sql ."<br/>";
			$result = mysqli_query ( $db, $sql );
			
			if (! $result) {
				die ( 'Query failed to execute for some reason' );
			}
			
			$row = mysqli_fetch_array ( $result, MYSQLI_NUM );
			// printf ("%s (%s)\n", $row[0], $row[1]);
			if (is_null ( $row )) {
				
				$sql = "INSERT INTO CoursesOffered (courseName,courseTitle,pre_reqs,semester_offered)  VALUES('" . $classes [1] [$i] . "','" . $classes [2] [$i] . "','" . $pre_req [1] [0] . "','" . $semesterOffered [0] [0] . "')";
				mysqli_query ( $db, $sql );
				mysqli_close ( $db );
				// echo $sql ."<br/>";
			} 

			else if (strpos ( $row ['semester_offered'], $semesterOffered [0] [0] )) { // checks if the semeseter is different, if it is, then it is appended
				
				$sql = "UPDATE CoursesOffered SET semester_offered = concat( semester_offered,' " . $semesterOffered [0] [0] . "') WHERE courseName LIKE '%" . $classes [1] [$i] . "%'";
				mysqli_query ( $db, $sql );
				mysqli_close ( $db );
			} else {
				
				echo $row ['semester_offered'] . " </br>" . $semesterOffered [0] [0] . " </br>";
			}
			
			// echo $classes[1][$i] . "<br/>";
			// echo $classes[2][$i] . "<br/>";
			// echo $pre_req[1][0];
			// echo"<br/><br/>";
		}
		echo '<a href="index.html">Home</a>';
	}
}
?>