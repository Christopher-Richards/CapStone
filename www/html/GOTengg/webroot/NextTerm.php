<?php
/**********************************************************
 * Note, Need to change queries to the userProgress table to use an SI
 * check redundancy in courses (GEOL 102 twice on my transcript)
 */
/**
 * *********************************************************
 */
if ((include '/var/www/html/GOTengg/src/Model.php') == TRUE) {
}
define ( 'MAX_CLASS_COUNT', '5' );
/**
 * ******************************************************************************************
 * This class is used to generate a list of semesters that are currently in the database
 */
class AvailibleSemesters {
	protected $checkSemesters;
	
	/*
	 * constructor
	 */
	function __construct() {
	}
	
	/*
	 * This function checks if the semesetersGenerated appear in the database
	 */
	function getAvailibleSemesters() {
		$connection = new dbConnection ();
		$connection->connect ();
		$db = $connection->getDb ();
		
		$this->generateSemesters ();
		
		for($i = 0; $i < count ( $this->checkSemesters ); $i ++) {
			for($j = 0; $j < 3; $j ++) {
				
				$sql = "SELECT * FROM CoursesOffered WHERE semester_offered LIKE '%" . $this->checkSemesters [$i] [$j] . "%'"; // search the DB for matches
				$result = mysqli_query ( $db, $sql );
				
				if (mysqli_num_rows ( $result ) != 0) {
					
					echo "<option value='" . $this->checkSemesters [$i] [$j] . "'>" . $this->checkSemesters [$i] [$j] . "</option>";
				}
			}
		}
	}
	
	/*
	 * This function takes the current year into account and generates a list of possible next semesters
	 */
	function generateSemesters() {
		$date = date ( "Y/m/d" ); // gets the current date
		$dateArray = explode ( "/", $date ); // splits the date into year/day/month
		                                     
		// years that need to be checked
		$checkyears = array (
				(intval ( $dateArray [0] ) + 1),
				(intval ( $dateArray [0] )),
				(intval ( $dateArray [0] ) - 1) 
		);
		
		for($i = 0; $i < 3; $i ++) {
			
			$this->checkSemesters [$i] = array (
					strval ( $checkyears [$i] . " Winter" ),
					strval ( $checkyears [$i] . " Fall" ),
					strval ( $checkyears [$i] . " Spring & Summer" ) 
			);
		}
	}
}

/**
 * ********************************************************************************************
 */
class NextSemester {
	protected $SI;
	protected $semester;
	protected $optimalClasses = array ();
	function __construct($Nsemester,$si) {
		echo "Calculating Next Semester: ";
		$this->setText ( $Nsemester );
		$this->setSI($si);
		echo $this->semester . "<br/></br>";
	}
	/*function redirect() {
		header ( "refresh:1;url=Classes.php" );
	}*/
	private function setSI($si) {
		$this->SI = $si;
	}
	
	private function setText($Nsemester) {
		$this->semester = $Nsemester;
	}
	/*public function getText() {
		return $this->semester;
	}*/
	
	/**
	 * **************************************************************
	 */
	// this function will attempt to determin the optimal classes for a student to take in the followin semester
	// assumes that the userProgress table contains the classes taken by the student compared to the schedule
	function optimalSemester() {
		$connection = new dbConnection ();
		$connection->connect ();
		$db = $connection->getDb ();
		
		$sql = "SELECT * FROM userProgress  WHERE SI = " .$this->SI;; // WHERE SI = 200306794"; // selects the info where the SI is the same *******************
		$result = mysqli_query ( $db, $sql );
		$numRows = mysqli_num_rows ( $result );
		
		if ($numRows == 0) { // check to make sure that the userProgress query was effective
			echo "error: no user progress known";
		} else {
		}
		
		while ( $evalClass = mysqli_fetch_assoc ( $result ) ) { // Creates a loop to loop through results to check if the student passed the given course
			
			$evalClass ['pass'] = $this->checkPass ( $evalClass ['grade'] ); // checks if the class has been completed
			
			if ($evalClass ['pass'] != 1) {
				
				$evalClass ['semNum'] = $this->getSemNum ( $evalClass ['semester'] );
				// preg_match ( '/\d/', $evalClass ['semester'], $matches ); // gets the suggested semester number from schedule
				// $evalClass ['semNum'] = $matches [0];
				
				$evalClass ['priority'] = 1; // sets the priority to the lowest level (int value)
				
				if (preg_match_all ( '/\w{2,4}\s\d{3}/', $evalClass ['class'], $className )) {
					$evalClass ['class'] = $className [0] [0];
				}
				
				$classInfo = array ();
				$this->getClassInfo ( $evalClass ['class'], $classInfo, $db );
				
				if (! empty ( $classInfo )) {
					
					// checks if the class is offered in the desired semester
					$evalClass ['offered'] = $this->checkOffered ( $classInfo ['semester_offered'] );
					
					// checks if the class is offered more than once per year
					// increments the priority by adding the return value of the function
					$evalClass ['priority'] = $evalClass ['priority'] + $this->checkFlexibility ( $classInfo ['semester_offered'] );
					
					// checks if the class is a pre_req for any other classes
					// adjusts the priority accordingly
					$evalClass ['priority'] = $evalClass ['priority'] + $this->checkPreReqs ( $evalClass ['class'], $db );
					
					// checks if the student has the pre-reqs reqired to take the class
					$evalClass ['has-reqs'] = $this->checkHasPreReqs ( $classInfo ['pre_reqs'], $db, "CourseInfo" );
					
					// echo $evalClass ['class'] . " Priority " . $evalClass ['priority'] . "Reqs? " . $evalClass ['has-reqs'] . "</br>";
				} else { // the class was not found in the DB, most likely an elective
					$evalClass ['priority'] = 1;
					$evalClass ['offered'] = "yes"; // possible elective string
					$evalClass ['has-reqs'] = "yes";
				}
				
				// this will determine if the student has passed the class
				
				if ($evalClass ['offered'] == "yes" && $evalClass ['has-reqs'] == "yes") {
					
					$this->addToList ( $evalClass ['class'], $evalClass ['priority'], $evalClass ['semNum'] );
					// echo $evalClass ['class'] . " Priority " . $evalClass ['priority'] . "</br>";
				}
				
				mysqli_free_result ( $classInfo );
			}
		}
		$this->sort ();
		$this->printResults ();
	}
	
	/**
	 * *********************
	 * This function returns the semester number as indicated in the semester string
	 * It takes a strring as a parameter and finds the value
	 * @
	 */
	function getSemNum($semString) {
		preg_match ( '/\d/', $semString, $matches ); // gets the suggested semester number from schedule
		return $matches [0];
	}
	/**
	 * ********************************************
	 * This function takes a reference to an array and class name
	 * It will return the semesters string that the class is offered in along with the pre-reqs
	 */
	function getClassInfo($className, &$info, $db) {
		$sql = "SELECT * FROM CoursesOffered Where courseName Like '%" . $className . "%'";
		$info = mysqli_query ( $db, $sql );
		$info = mysqli_fetch_assoc ( $info );
	}
	
	/**
	 * *************************************************************
	 * This function searches a string for matches with the semester
	 * It returns "yes" if there is a match and "no" if there isn't
	 */
	function checkOffered($semString) {
		if (strpos ( $semString, $this->semester ) !== FALSE) {
			
			$evalClass ['offered'] = "yes"; // boolean meaning the class is offered
			return "yes";
		} else {
			$evalClass ['offered'] = "no"; // class is not offered in the given semester
			return "no";
		}
	}
	
	/**
	 * *************************************************************
	 */
	// This function take in the value of the semester offered of a class
	// This function will check if a class is flexible
	// This means that teh class is offered more that once per year.
	// if it is then the return value is 1 and if it isn't, then the return value is 0
	function checkFlexibility($semString) {
		$frequency = 0; // this value will count the frequency of a course
		if (strpos ( $semString, "Winter" ) !== FALSE) {
			$frequency ++;
		}
		if (strpos ( $semString, "Spring" ) !== FALSE) {
			$frequency ++;
		}
		if (strpos ( $semString, "Fall" ) !== FALSE) {
			$frequency ++;
		}
		
		if ($frequency > 1) {
			return 0;
		} else {
			return 1;
		}
	}
	
	/**
	 * *************************************************************
	 */
	// This function will check if a class pre_req for any other classes.
	// This is done by querying the CoursesOffered table to look for matches in the pre_reqs colum
	// if no matches then the class is not a pre_req and function will return 0
	// if there are matches, the function must check to see if the class is required by the schedule
	// or if it is an elective. in the case that the class is a pre_req for an elective, the function returns 1
	// in the case it is a pre_req for a required class, then the function returns 2
	function checkPreReqs($class, $db) {
		$sql = "SELECT * FROM CoursesOffered WHERE pre_reqs LIKE '%" . $class . "%'";
		$result = mysqli_query ( $db, $sql );
		
		if (mysqli_num_rows ( $result ) == 0) {
			return 0; // the course is not a pre_req
		} 

		else {
			
			while ( $required = mysqli_fetch_assoc ( $result ) ) { // gets a list of classes that this class is a pre_req for
				
				$sql = "SELECT * FROM userProgress WHERE class LIKE '%" . $required ['courseName'] . "%' AND SI ='".$this->SI."'";
				$RequiredResult = mysqli_query ( $db, $sql );
				
				if (mysqli_num_rows ( $RequiredResult ) > 0) {
					return 2; // the course is a pre-req for a required course
				}
			}
			
			return 1; // the class is a pre-req for an elective
		}
	}
	
	/**
	 * *************************************************************
	 */
	// This function checks if a student has passed the course based on their grade
	// returns "pass" if they have completed the course
	// returns "0" if they haven't signed up for the course
	// returns "Not Completed if they have signed up for the course
	function checkPass($grade) {
		if ((intval ( $grade ) >= 50 && intval ( $grade ) <= 100) || strpos ( $grade, "P" ) !== False) {
			return 1;
		} else if ($grade == "Not Completed") {
			return "Not Completed"; // the student has registerd but not completed
		} else {
			return 0; // students hasn't registered
		}
	}
	
	/**
	 * ********************************************************
	 * Note Check concurrency, must be updated
	 */
	//
	// This function will check if the user has the reqired pre-reqs to take the class at this time.
	function checkHasPreReqs($preReq, $db, $table) {
		
		// echo $preReq . "</br>"; /*************************************/ pre req fail?
		if ($preReq == "" || preg_match ( '/\w{2,4}\s\d{3}/', $preReq ) == 0) {
			return "yes"; // this means that the class has no pre-reqs
		} 

		else if (strpos ( $preReq, "concurrent" ) !== FALSE || strpos ( $preReq, "Concurrent" ) !== FALSE) { // this is temp until i make a functions to check the concurrency
			return "yes";
		} else if (strpos ( $preReq, "One of" ) !== FALSE) {
			if ($this->checkPreReqMatches ( $preString, $db, $table )) {
				return "yes";
			}
		} 

		else if (strpos ( $preReq, "or" ) !== FALSE) {
			$dividedPreReq = preg_split ( '/\sor\s/', $preReq ); // special case is considered where the you can take one pre-req OR another
			$flag = "no"; // pre set flag meaning you don't have the pre reqs yet
			
			for($k = 0; $k < count ( $dividedPreReq ); $k ++) {
				
				// echo $dividedPreReq[$k] . " <br/>";
				if ($this->checkPreReqMatches ( $dividedPreReq [$k], $db, $table ) == "yes") { // calls a function to check subStrings
					
					return "yes"; // checks one substring == "yes" then the student can take the class
				}
			}
		} else {
			if ($this->checkPreReqMatches ( $preString, $db, $table )) {
				return "yes";
			}
		}
		
		return "no";
	}
	
	/**
	 * *******************************************************
	 * Returns yes or no depending on whether the pre reqs are in the user progress table
	 * the function returns "no" if the student is missing a pre-req
	 */
	function checkPreReqMatches($preString, $db, $table) {
		$flag = "yes"; // we first assume the class can be taken until we prove otherwise
		
		preg_match_all ( '/\w{2,4}\s\d{3}/', $preString, $matches );
		
		if (! empty ( $matches )) {
			
			for($j = 0; $j < count ( $matches ); $j ++) { // check user progress to see if the student has taken the class
				
				$sql = "SELECT * FROM " . $table . " WHERE courseTitle LIKE '%" . $matches [0] [$j] . "%' AND SI = '".$this->SI."'"; // ////add ID
				$RequiredResults = mysqli_query ( $db, $sql );
				
				while ( $RequiredResults = mysqli_fetch_assoc ( $RequiredResults ) ) { //
				                                                                       
					// calls a function to check if the class was taken and passed or currently registered by the student
					if (! empty ( $RequiredResults ) && ($this->checkPass ( $RequiredResults ['grade'] ) != 0)) {
						// do nothing because the flag will already be set
					} else {
						$flag = "no"; // the student is missing a pre-req
					}
				}
			}
		} else {
			$flag = "yes"; // no class pre-reqs in this string thus the student can still take the class
		}
		
		return $flag;
	}
	
	/**
	 * *************************************************************
	 * This function will add the class to the array if it is a viable class
	 */
	function addToList($name, $priority, $semNum) {
		
		// echo $name . " Priority value " .$priority . " ". $semNum ."</br>";
		$this->optimalClasses [] = array (
				$name,
				$priority,
				$semNum 
		);
	}
	
	/**
	 * ***************************************************
	 * This functions will print the results of a the array given
	 * It will also order the array from highest priority to lowest
	 * If there is conflict, the semester that the class is offered in will be
	 * taken into account as well
	 */
	function printResults() {
		// WILL ACCOMPLISH LATER
		echo "The classes that are optimal next semester are: </br></br> ";
		
		for($i = 0; $i < count ( $this->optimalClasses ); $i ++) {
			echo $this->optimalClasses [$i] [0] . "     Priority-value:  " . $this->optimalClasses [$i] [1] . "</br>";
		}
	}
	
	/**
	 * ************************************************
	 * this function will sort the array into prioriy value and semester number
	 */
	function sort() {
		for($j = count ( $this->optimalClasses ) - 1; $j > 0; $j --)
			for($i = 0; $i < $j; $i ++) {
				if (($this->optimalClasses [$i] [1] < $this->optimalClasses [$i + 1] [1]) || ($this->optimalClasses [$i] [1] == $this->optimalClasses [$i + 1] [1] && $this->optimalClasses [$i] [2] > $this->optimalClasses [$i + 1] [1])) {
					
					$this->swap ( $i, $i + 1 );
				}
			}
		
		return;
	}
	/**
	 * ************************************************
	 * swap function to re arrange the array
	 * swaps the position of the 2 keys
	 */
	function swap($key1, $key2) {
		$temp = array (
				$this->optimalClasses [$key1] [0],
				$this->optimalClasses [$key1] [1],
				$this->optimalClasses [$key1] [2] 
		);
		
		$this->optimalClasses [$key1] [0] = $this->optimalClasses [$key2] [0];
		$this->optimalClasses [$key1] [1] = $this->optimalClasses [$key2] [1];
		$this->optimalClasses [$key1] [2] = $this->optimalClasses [$key2] [2];
		
		$this->optimalClasses [$key2] [0] = $temp [0];
		$this->optimalClasses [$key2] [1] = $temp [1];
		$this->optimalClasses [$key2] [2] = $temp [1];
	}
}

// TTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTT
/*
 * This function will print the selescted schedule
 */
class SchedulePlanner {
	private $semester_count;
	protected $classes = array ();
	protected $class_count;
	function __construct() {
		// echo "Loading please wait ...";
		$this->class_count = 0;
		$this->semester_count = 0;
	}
	function printClasses() {
		
		// if ($_SERVER ["REQUEST_METHOD"] == "POST") {
		
		// set instance of the db
		$connection = new dbConnection ();
		$connection->connect ();
		$db = $connection->getDb ();
		
		// ////////////// Need to update this with SI value instead once it is implemented////////////////////
		$sql = "SELECT * FROM SuggestedSchedule"; // search the DB for matches
		mysqli_free_result ( $result );
		$result = mysqli_query ( $db, $sql );
		
		if ($result) { // If query successfull
			
			while ( $row = mysqli_fetch_assoc ( $result ) ) { // Creates a loop to loop through results
			                                                  
				// $this->classes [$this->class_count] ['sem_num'] = $this->getSemesterNumber ( $row ['semester'] );
				$this->classes [$this->class_count] ['pass'] = $row ['pass'];
				$this->classes [$this->class_count] ['class'] = $row ['class'];
				$this->classes [$this->class_count] ['id'] = $row ['id'];
				$this->classes [$this->class_count] ['unAccounted'] = $row ['unAccounted'];
				$this->classes [$this->class_count] ['priority'] = $row ['priority'];
				$this->classes [$this->class_count] ['term'] = $row ['term'];
				$this->class_count ++;
			}
			
			for($i = 1; $i <= $this->semester_count; $i ++) {
				echo "	<h3>Semester " . $i . " </h3>
						<ul class='list-group' id='semester" . $i . "'  >";
				
				for($j = 0; $j < $this->class_count; $j ++) {
					// echo $this->classes_count;
					if (intval ( $this->classes [$j] ['sem_num'] ) == $i) {
						
						// echo $this->classes[$j] ['class'];
						
						$this->classes [$j] ['html'] = $this->getHTML ( $this->classes [$j] ['pass'], $this->classes [$j] ['class'], $this->classes [$j] ['id'], $this->classes [$j] ['sem_num'] );
						
						echo $this->classes [$j] ['html'];
					}
				}
				echo "</ul>";
			}
		} else {
			echo "No Results Found";
		}
	}
	
	/**
	 * This function returns the semeter number from the string containing the semester name
	 * It will also update the semester count if a new semester has been found
	 */
	function getSemesterNumber($sem_string) {
		$sem_num = array ();
		preg_match_all ( '/\d{1,2}/', $sem_string, $sem_num );
		
		if (intval ( $sem_num [0] [0] ) > $this->semester_count) {
			$this->semester_count = intval ( $sem_num [0] [0] );
		}
		
		return $sem_num [0] [0];
	}
	
	/*
	 * This function will take a value either "yes" or "no" as a parameter
	 * It will determine the HTML string that will be generated
	 * The class name and the id will be needed to add to the html
	 */
	function getHTML($pass, $class, $id, $sem_num) {
		$html = "";
		
		if ($pass == 1) {
			$html = "
			<ul class='alists' id= '" . $id . "'>
			<li class='list-group-item list-group-item-success'>" . $class . "</li>
			<li class='list-group-item list-group-item-success'>Completed</li>
			</ul>";
		} else if ($pass == 0) {
			
			$html = "
			<ul class='alists' id='" . $id . "'  value='incomplete'>
			<li class='list-group-item list-group-item-warning'>" . $class . "</li>
			<li class='list-group-item list-group-item-warning'>incomplete</li>
					<select value ='semester" . $sem_num . "' onchange='swapSemester(" . $id . ",this.value)'>";
			
			for($k = 1; $k <= $this->semester_count; $k ++) {
				$html .= "<option value='semester" . $k . "'>Semester" . $k . "</option>";
			}
			
			$html .= "</select></ul>";
		}
		
		return $html;
	}
}

/**
 * **************************************************************************************
 * This Class will be used to determine optimal route fot the student to take.
 */
class FutureSchedule extends NextSemester {
	private $printedClasses = array ();
	private $unPrintedClasses = array ();
	private $unAccountedClasses = array ();
	protected $semester = array (); // holds the year and the semester value (10,20,30) , both are int values
	function __construct($si) {
		$this->setSI($si);
		$this->setNextSemester (); // sets the current next semester
	}
	function printOptimalSchedule() {    // getOptimalSchedul
		$connection = new dbConnection ();
		$connection->connect ();
		$db = $connection->getDb ();
		
		$sql = "SELECT * FROM userProgress WHERE SI = " .$this->SI;// WHERE SI = 200306794"; // selects the info where the SI is the same *******************
		$result = mysqli_query ( $db, $sql );
		$numRows = mysqli_num_rows ( $result );
		
		if ($numRows == 0) { // check to make sure that the userProgress query was effective
			echo "error: no user progress known";
		} else {
		}
		
		while ( $evalClass = mysqli_fetch_assoc ( $result ) ) { // Creates a loop to loop through results to check if the student passed the given course
			
			$evalClass ['pass'] = $this->checkPass ( $evalClass ['grade'] );
			
			// updates the class name, if it was modified
			if (preg_match_all ( '/\w{2,4}\s\d{3}/', $evalClass ['class'], $className )) {
				$evalClass ['class'] = $className [0] [0];
			}
			$evalClass ['term'] = $this->getTerm ( $evalClass ['class'], $db );
			
			// send the passed classes to the printed array
			// insert into the DB
			// echo "Class: " . $evalClass ['class'] . " pass: " . $evalClass ['pass'] . " </br>";
			// echo "please";
			
			if ($evalClass ['pass'] == 1) {
				
				// $this->insertToDB($class, $term, $priority, $pass, $unAccounted, $db)
				$this->insertToDB ( $evalClass ['class'], $evalClass ['term'], 0, 1, 0, 0, $db );
				$this->toPrintedClasses ( $evalClass ['class'], $evalClass ['term'], 0, true );
			}  // classes that are currently in progress..
else if ($evalClass ['term'] != "") {
				
				$this->insertToDB ( $evalClass ['class'], $evalClass ['term'], 0, 0, 0, 1, $db );
			} else 

			{
				
				$evalClass ['semNum'] = $this->getSemNum ( $evalClass ['semester'] );
				$evalClass ['priority'] = 1;
				
				$classInfo = array ();
				$this->getClassInfo ( $evalClass ['class'], $classInfo, $db );
				
				if (! empty ( $classInfo )) {
					// echo $classInfo ['semester_offered'] . "</br>";
					$evalClass ['semester_offered'] = $this->getSemOffered ( $classInfo ['semester_offered'] );
					
					$evalClass ['priority'] = $evalClass ['priority'] + $this->checkFlexibility ( $evalClass ['semester_offered'] );
					
					$evalClass ['priority'] = $evalClass ['priority'] + $this->checkPreReqs ( $evalClass ['class'], $db );
					
					// checks if the student has the pre-reqs reqired to take the class
					// $this->toUnPrintedClasses ( $evalClass ['class'], $evalClass ['term'], $evalClass ['priority'], $evalClass ['semNum'], $evalClass ['semester_offered'] );
					// $evalClass['has-reqs']);
					$evalClass ['pre_reqs'] = $classInfo ['pre_reqs'];
					
					// echo "Pre_reqs : ". $classInfo ['pre_reqs']. " </br>";
				} else {
					$evalClass ['semester_offered'] = "/10/20/30";
					$evalClass ['pre_reqs'] = "";
				}
				
				$this->toUnPrintedClasses ( $evalClass ['class'], intval ( $evalClass ['priority'] ), intval ( $evalClass ['semNum'] ), $evalClass ['semester_offered'], $evalClass ['pre_reqs'] );
				// echo $evalClass ['pass'] . " " . $evalClass ['class'] . " " . $evalClass ['term'] . " " . $evalClass ['priority'] . " " . $evalClass ['semNum'] . " " . $evalClass ['pre_reqs'] . " </br>";
			}
		}
		
		$this->getClassNotInProgress ( $db );
		// echo " </br>Un accounted for Classes</br>";
		// print_r ( $this->unPrintedClasses );
		// echo count ( $this->unPrintedClasses );
		// $this->removeRow ( 0 );
		// echo "</br></br>";
		// print_r ( $this->unPrintedClasses );
		// echo count ( $this->unPrintedClasses );
		// echo "</br></br>";
		$this->iterateSemester ();
	}
	
	/**
	 * ********************************************************
	 * This function interates through the unprintedClasses to add them to the printedClass array
	 */
	function iterateSemester() {
		$connection = new dbConnection ();
		$connection->connect ();
		$db = $connection->getDb ();
		
		// gets the current semester
		$termArray = $this->getSemester ();
		print_r ( $termArray );
		
		$term = implode ( "", $termArray );
		// echo $term . " HOHOHO </br>";
		// echo count ( $this->unPrintedClasses );
		
		// for ($k=0; $k<2; $k++){
		while ( count ( $this->unPrintedClasses ) > 0 ) {
			
			// echo "This Semester < /br>";
			print_r ( $this->semester );
			// get the current term
			/*
			 * $termArray = $this->getSemester ();
			 * $term = implode ( "", $termArray ); // gets the term in string format
			 * $term = intval ( $term );
			 */
			
			// echo $term . " Term </br>";
			$leftOver = 0;
			
			// echo $leftOver . " Left over< /br>";
			
			// check if current semester has 5 classes // if not add untill 5 classes
			// check if the count changed in the last iteration
			
			// echo "</br></br> WHile";
			// echo $this->getSpacesLeft ( $term ) . " </br>";
			// echo MAX_CLASS_COUNT;
			// echo " " .$leftOver . " !=";
			// echo $this->getSpacesLeft ( $term ) . " </br>";
			
			while ( $this->getSpacesLeft ( $term ) > 0 && $leftOver != $this->getSpacesLeft ( $term ) ) {
				
				$leftOver = $this->getSpacesLeft ( $term );
				// could have problem with concurrent enrolement
				// loops through the remaining classes
				
				$nextClass = null; // this will hold the key of the next value to b inserted
				
				for($j = 0; $j < count ( $this->unPrintedClasses ); $j ++) {
					
					// check if the semester matches
					
					// echo $this->unPrintedClasses[$j][4]. " </br>^^^pre_reqs ^^^</br>";
					// echo strval ( $termArray ['sem'] ) . " </br>^^TErm</br>";
					// echo $this->checkHasPreReqs ( $preReq, $db, "SuggestedSchedule" ) . " </br><br/>";
					
					// /search for matching semester
					if (strpos ( $this->unPrintedClasses [$j] [3], strval ( $termArray ['sem'] ) ) !== FALSE && $this->checkHasPreReqs ( $this->unPrintedClasses [$j] [4], $db, "SuggestedSchedule" ) == "yes") {
						
						// check if the nextclass is empty
						if (empty ( $nextClass )) {
							
							$nextClass = array (
									$this->unPrintedClasses [$j] [0],
									$this->unPrintedClasses [$j] [1],
									$this->unPrintedClasses [$j] [2],
									FALSE 
							);
							$key = $j;
							print_r ( $nextClass );
						} else if (intval ( $nextClass [1] ) < intval ( $this->unPrintedClasses )) {
							
							// change the next class to be printed
							$nextClass [0] = $this->unPrintedClasses [$j] [0];
							$nextClass [1] = $this->unPrintedClasses [$j] [1];
							$nextClass [2] = $this->unPrintedClasses [$j] [2];
							$key = $j;
							
							// check if this class is optimal....
						}
						// $leftOver ++; // increments the number of classes that can still be added
					}
				}
				
				if (! empty ( $nextClass )) {
					
					$this->insertToDB ( $nextClass [0], $term, $nextClass [1], 0, 0, 0, $db );
					$this->removeRow ( $key );
					$leftOver ++;
				}
			}
			// increment the semester
			$this->incrementSemester ();
			$termArray = $this->getSemester ();
			$term = implode ( "", $termArray ); // gets the term in string format
			$term = intval ( $term );
		}
	}
	
	// DELETE * FROM SuggestedSchedule WHERE pass = 0
	
	/**
	 * **************************?
	 * This funciton is used to unset a row in the unPrinted Array
	 * it will set the data to be the last element in the array and it will then delete the last element
	 */
	function removeRow($key) {
		$length = count ( $this->unPrintedClasses );
		
		if (($length - 1) == $key) {
			unset ( $this->unPrintedClasses [$key] );
		} else {
			$this->unPrintedClasses [$key] [0] = $this->unPrintedClasses [$length - 1] [0];
			$this->unPrintedClasses [$key] [1] = $this->unPrintedClasses [$length - 1] [1];
			$this->unPrintedClasses [$key] [2] = $this->unPrintedClasses [$length - 1] [2];
			$this->unPrintedClasses [$key] [3] = $this->unPrintedClasses [$length - 1] [3];
			$this->unPrintedClasses [$key] [4] = $this->unPrintedClasses [$length - 1] [4];
			unset ( $this->unPrintedClasses [$length - 1] );
		}
	}
	
	/**
	 * ********************************
	 * This function inserts the classes into the DB
	 * (string,string,int,int,bool, bool,$db)
	 */
	function insertToDB($class, $term, $priority, $pass, $unAccounted, $inProgress, $db) {
		$sql = "INSERT INTO SuggestedSchedule (id, SI, courseTitle, term, pass, priority, semNum, unAccounted, inProgress) 
				VALUES (NULL, '".$this->SI."', '" . $class . "', '" . strval ( $term ) . "', '" . $pass . "', '" . $priority . "', '', '" . $unAccounted . "','" . $inProgress . "')";
		
		// echo $sql;
		// MAKE A CHECK TO UPDATE
		// mysqli_query ( $db, $sql );
	}
	
	/**
	 * *******************************************************
	 * This function gets the number of classes that are still able to be filled by the current semester
	 *
	 *
	 * ////// max Class count
	 */
	function getSpacesLeft($term) {
		$numOfClasses = MAX_CLASS_COUNT;
		
		for($i = 0; $i < count ( $this->printedClasses ); $i ++) {
			if ($this->printedClasses [$i] [1] == $term) {
				$numOfClasses --;
			}
		}
		return intval ( $numOfClasses );
		
	}
	
	/**
	 * ****************************************
	 * This function will take in a string containing a list of when the class is offere
	 * It will serach the String for "winter", "Fall", and "Spring"
	 * a value will be assigned for each accordingly
	 */
	function getSemOffered($semString) {
		$offeredIn = "";
		if (strpos ( $semString, "Winter" ) !== FALSE) {
			$offeredIn .= "/10";
		}
		if (strpos ( $semString, "Spring" ) !== FALSE) {
			$offeredIn .= "/20";
		}
		if (strpos ( $semString, "Fall" ) !== FALSE) {
			$offeredIn .= "/30";
		}
		return $offeredIn;
	}
	
	/**
	 * ****************************************************
	 * This function will add a class to the printedClasses array
	 * *Note the priority is only there for classes that have yet to be taken
	 * If the class has been take, the priority will be 0
	 * (string, string, int,bool)
	 */
	function toPrintedClasses($class, $term, $priority, $pass) {
		$this->printedClasses [] = array (
				$class,
				$term,
				$priority,
				$pass 
		);
	}
	
	/**
	 * ****************************************************
	 * This function will add a class to the unPrintedClasses array
	 * (String, int,int,string,string)
	 */
	function toUnPrintedClasses($class, $priority, $sem_num, $sem_offered, $pre_reqs) { // $has_reqs) {
		$this->unPrintedClasses [] = array (
				$class,
				$priority,
				$sem_num,
				$sem_offered,
				$pre_reqs 
		);
		// $has_reqs
	}
	
	/**
	 * ****************************************************
	 * This function will get the term that the class was taken by the student
	 * Returns the value as an int or null if the class wasn't found.
	 */
	function getTerm($class, $db) {
		$sql = "SELECT term FROM CourseInfo WHERE courseTitle LIKE '%" . $class . "%' AND SI = '" .$this->SI. "'"; // // add SI
		$result = mysqli_query ( $db, $sql );
		$term = mysqli_fetch_assoc ( $result );
		$rows = mysqli_num_rows ( $result );
		
		if ($rows == 1) {
			return (intval ( $term ['term'] ));
		} else {
			return null;
		}
	}
	/*
	 * This function will return a string containing the value of the next semester
	 */
	function setNextSemester() {
		$date = date ( "Y/m/d" ); // gets the current date
		
		$dateArray = explode ( "/", $date ); // splits the date into year/day/month
		
		if (intval ( $dateArray [1] ) >= 8 && intval ( $dateArray [1] < 10 )) { // value for fall
			$this->semester ['year'] = intval ( $dateArray [0] );
			$this->semester ['sem'] = 30;
		} else if (intval ( $dateArray [1] ) >= 2 && intval ( $dateArray [1] < 8 )) { // value for spring
			$this->semester ['year'] = intval ( $dateArray [0] );
			$this->semester ['sem'] = 20;
		} else if (intval ( $dateArray [1] ) >= 10 && intval ( $dateArray [1] < 2 )) { // value for winter
			$this->semester ['year'] = intval ( $dateArray [0] );
			$this->semester ['sem'] = 10;
		}
	}
	/*
	 * This function increments the current semester
	 */
	function incrementSemester() {
		if ($this->semester ['sem'] == 10) {
			$this->semester ['sem'] = 20;
		} else if ($this->semester ['sem'] == 20) {
			$this->semester ['sem'] = 30;
		} else if ($this->semester ['sem'] == 30) {
			$this->semester ['year'] ++;
			$this->semester ['sem'] = 10;
		}
	}
	/*
	 * getter for next semester
	 */
	function getSemester() {
		return $this->semester;
	}
	
	/**
	 * *************************************************************
	 * This function will print the the classes in the printedClass array with the given term.
	 * This function takes a term as a parameter
	 */
	function printTerm($term) {
		for($i = 0; $i < count ( $this->printedClasses ); $i ++) {
			if ($this->printedClasses [$i] [1] == $term) {
				echo $this->printedClasses [$i] [0] . " " . $this->printedClasses [$i] [1] . " " . $this->printedClasses [$i] [2] . " </br>";
			}
		}
		print_r ( $this->printedClasses );
	}
	
	/**
	 * **************************
	 *
	 * gets the left over classes from userProgress
	 */
	public function getClassNotInProgress($db) {
		$sql = "SELECT CourseInfo.courseTitle, CourseInfo.term, CourseInfo.grade from CourseInfo LEFT JOIN userProgress ON userProgress.class LIKE CONCAT('%', CourseInfo.courseTitle, '%') WHERE userProgress.class IS NULL";
		
		$result = mysqli_query ( $db, $sql );
		
		While ( $row = mysqli_fetch_assoc ( $result ) ) {
			$this->setUnAccountedClasses ( $row ['courseTitle'], $row ['term'], 0 );
			
			if ($this->checkPass ( $row ['grade'] )) {
				$this->insertToDB ( $row ['courseTitle'], $row ['term'], 0, 1, 1, 0, $db );
			}
			
			// echo $result['courseTitle']. " ". $result ['term'];
		}
	}
	
	/**
	 * *****************************************************
	 * This function sets the un accounted for classes.
	 */
	function setUnAccountedClasses($class, $term, $priority) {
		$this->unAccountedClasses [] = array (
				$class,
				$term,
				$priority 
		);
	}
}

?>

















