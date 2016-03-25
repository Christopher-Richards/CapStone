<?php
// include '/var/www/html/GOTengg/config/dbConnection.php';
class dbConnection {
	protected $db;
	public function connect() {
		$dataB = mysqli_connect ( "localhost", "root", "ense400", "StudentInfo" ) or die ( "could not connect to the database: Error " . mysqli_error ( $db ) );
		$this->setDb ( $dataB );
	}
	private function setDb($dataB) {
		$this->db = $dataB;
	}
	public function getDb() {
		return $this->db;
	}
	public function test() {
		$t = 9;
		return $t;
	}
}
class StudentParse {
	protected $SI;
	protected $text;
	function __construct($conText,$si) {
		echo "in constructor";
		$this->setSI($si);
		$this->setText ( $conText );
	}
	function studentParse() {
		$connection = new dbConnection ();
		$connection->connect ();
		$db = $connection->getDb ();
		
		$sql = "SELECT * FROM StuInfo WHERE SI = " .$this->SI;// 200306794"; // selects the info where the SI is the same *******************
		$result = mysqli_query ( $db, $sql );
		$numRows = mysqli_num_rows ( $result );
		
		if ($numRows > 0) {
			// Already info in there, need to delete (probably caused by someone pressing the back button
			$deleteStu = "DELETE FROM StuInfo WHERE SI = " .$this->SI;//200306794"; // *****************************
			mysqli_query ( $db, $deleteStu );
			$deleteCo = "DELETE FROM CourseInfo WHERE SI = " .$this->SI;//200306794"; // ***************************
			mysqli_query ( $db, $deleteCo );
		} else {
		}
		// $this->text = mysql_real_escape_string($this->text);// escape characters
		$this->text = str_replace ( "'", "''", $this->text ); // replace ' so that sql can query
		
		$ID = array ();
		preg_match ( '/\d{9}/', $this->text, $ID ); // this will get the SID of the student
		
		$name = array ();
		preg_match ( '/Name:(.*?)\n/', $this->text, $name ); // extract name from transcript
		                                                   // $name[1]=str_replace (" " , "" , $name[1]); //removes spaces and comma's
		                                                   // $name[1]=str_replace ("," , "" , $name[1]);
		
		$major = array ();
		preg_match ( '/Major.{7}(.*?)Year/', $this->text, $major ); // extract major from transcript
		
		$startYear = array (); // gets the start year , Loops through the classes on the transcript to find the earliest date
		preg_match_all ( '/(\d{4})\s(Fall|Winter|Spring & Summer)/', $this->text, $startYear );
		$year = 9999;
		for($i = 0; $i < count ( $startYear [1] ); $i ++) {
			if ($startYear [1] [$i] < $year) {
				$year = $startYear [1] [$i];
			}
		}
		
		// adds the information to the database if the data is good
		$sql = "INSERT INTO StuInfo (SI, name, startYear, major)  VALUES('$ID[0]','$name[1]','$year','$major[1]')";
		mysqli_query ( $db, $sql );
		
		$TermString = array ();
		$TermString = preg_split ( '/(\d{4})\s(Fall|Winter|Spring & Summer)/', $this->text );
		
		for($i = 0; $i < count ( $startYear [0] ); $i ++) {
			preg_match_all ( '/(\w{2,4}\t\d{1,3})\t\d{3}(.*?)(Registered|\d{1,3}|\s[W]\s|\s[P]\s|\s[F]\s)/', $TermString [$i], $classes ); // creates an array of classes
			
			if (! empty ( $classes [0] )) {
				
				for($j = 0; $j < count ( $classes [0] ); $j ++) {
					$courseID = ( string ) $classes [1] [$j];
					$courseTitle = ( string ) $classes [2] [$j];
					$grade = ( string ) $classes [3] [$j];
					
					if ($grade == "Registered") // this is to convert the string to the same formate as the admin transcript since they use the words "Not Completed instead
{
						$grade = "Not Completed";
					}
					
					// This will convert the string containing the semester and year into the term number exp :201610 for winter 2016
					if ($startYear [2] [$i - 1] == "Winter") {
						$term = ( string ) $startYear [1] [$i - 1] . "10";
					} else if ($startYear [2] [$i - 1] == "Fall") {
						$term = ( string ) $startYear [1] [$i - 1] . "30";
					} else {
						$term = ( string ) $startYear [1] [$i - 1] . "20";
					}
					
					$sql = "INSERT INTO CourseInfo (SI,courseTitle, term, grade)  VALUES('"  .$this->SI . "','" . $courseID . "','" . $term . "','" . $grade . "')";
					mysqli_query ( $db, $sql );
				}
			}
		}
		
		mysqli_close ( $db );
	}
	function redirect() {
		header ( "refresh:1;url=Classes.php" );
	}
	private function setText($conText) {
		$this->text = $conText;
	}
	private function setSI($si){
		$this->SI= $si;
	}
	public function getText() {
		return $this->text;
	}
}
class AdminParse {
	protected $SI;
	protected $text;
	function __construct($conText,$si) {
		echo "in constructor --";
		$this->setText ( $conText );
		$this->setSI($si);
	}
	function parse() {
		// echo "THIS->TEXT: " . $this->text;
		$connection = new dbConnection ();
		
		$connection->connect ();
		// $connection->connect();
		$db = $connection->getDb ();
		// echo $db2;
		
		// $db = mysqli_connect("localhost", "root", "ense400", "StudentInfo") or die("could not connect to the database: Error " . mysqli_error($db));
		
		$sql = "SELECT * FROM StuInfo  WHERE SI = " .$this->SI;
		$result = mysqli_query ( $db, $sql );
		$numRows = mysqli_num_rows ( $result );
		
		if ($numRows > 0) {
			// Already info in there, need to delete (probably caused by someone pressing the back button
			$deleteStu = "DELETE FROM StuInfo  WHERE SI = " .$this->SI;
			mysqli_query ( $db, $deleteStu );
			$deleteCo = "DELETE FROM CourseInfo  WHERE SI = " .$this->SI;
			mysqli_query ( $db, $deleteCo );
		} else {
		}
		
		// $text = $_POST["studenttext"];
		$parseTextName = array (); // gets the start year
		preg_match_all ( '/(\d{9}.*)/', $this->text, $parseTextName );
		$temp = $parseTextName [0] [0];
		$pos = strpos ( $temp, "\t" );
		$id = substr ( $temp, 0, $pos );
		$id = trim ( $id );
		
		$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
		
		$pos = strpos ( $temp, "\t" );
		
		$name = substr ( $temp, 0, $pos );
		$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
		
		$pos = strpos ( $temp, "\t" );
		
		$campus = substr ( $temp, 0, $pos );
		$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
		$pos = strpos ( $temp, "\t" );
		
		$college = substr ( $temp, 0, $pos );
		$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
		
		$pos = strpos ( $temp, "\t" );
		
		$degree = substr ( $temp, 0, $pos );
		$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
		
		$pos = strpos ( $temp, "\t" );
		
		$major = substr ( $temp, 0, $pos );
		$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
		
		$level = $temp;
		
		$UserInfo = "INSERT INTO StuInfo (SI,name,startYear,major) VALUES ( '$this->SI', '$name','0000','$major')";
		echo $UserInfo . "<br>";
		if (mysqli_query ( $db, $UserInfo )) {
		} else {
			echo "Error: " . $UserInfo . "<br>" . mysqli_error ( $conn );
		}
		$parseText = array (); // gets the start year
		preg_match_all ( '/(\d{6}(.*?)\t\w{2,4}\s*\d{1,3}).*/', $this->text, $parseText );
		for($i = 0; $i < count ( $parseText [1] ); $i ++) {
			$line = $parseText [0] [$i];
			if (strpos ( $line, 'Registered' ) !== false) {
				// Currently taking
				$temp = $line;
				$pos = strpos ( $temp, "\t" );
				$code = substr ( $temp, 0, $pos );
				$code = trim ( $code );
				$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
				$pos = strpos ( $temp, "\t" );
				$subject = substr ( $temp, 0, $pos );
				$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
				$pos = strpos ( $temp, "\t" );
				$number = substr ( $temp, 0, $pos );
				$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
				$pos = strpos ( $temp, "\t" );
				$title = substr ( $temp, 0, $pos );
				$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
				$pos = strpos ( $temp, "\t" );
				$status = substr ( $temp, 0, $pos );
				$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
				$pos = strpos ( $temp, "\t" );
				$campus = substr ( $temp, 0, $pos );
				$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
				$cHours = $temp;
				$subject = trim ( $subject );
				$number = trim ( $number );
				$courseTitle = $subject . " " . $number;
				$CourseInfo = "INSERT INTO CourseInfo (SI, courseTitle, term,grade) VALUES ('$this->SI', '$courseTitle', '$code','Not Completed')";
				if (mysqli_query ( $db, $CourseInfo )) {
				} else {
					echo "Error: " . $CourseInfo . "<br>" . mysqli_error ( $db );
				}
			} else {
				$temp = $line;
				$pos = strpos ( $temp, "\t" );
				$code = substr ( $temp, 0, $pos );
				$code = trim ( $code );
				$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
				$pos = strpos ( $temp, "\t" );
				$subject = substr ( $temp, 0, $pos );
				$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
				$pos = strpos ( $temp, "\t" );
				$number = substr ( $temp, 0, $pos );
				$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
				$pos = strpos ( $temp, "\t" );
				$title = substr ( $temp, 0, $pos );
				$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
				$pos = strpos ( $temp, "\t" );
				$cHours = substr ( $temp, 0, $pos );
				$temp = substr ( $temp, $pos + 1, strlen ( $temp ) );
				$grade = $temp;
				$subject = trim ( $subject );
				$number = trim ( $number );
				$courseTitle = $subject . " " . $number;
				$CourseInfo = "INSERT INTO CourseInfo (SI, courseTitle, term,grade) VALUES ('$this->SI', '$courseTitle', '$code','$grade')";
				if (mysqli_query ( $db, $CourseInfo )) {
				} else {
					echo "Error: " . $CourseInfo . "<br>" . mysqli_error ( $db );
				}
			}
		}
		mysqli_close ( $db );
	}
	private function setSI($si){
		$this->SI= $si;
	}
	function redirect() {
		header ( "refresh:1;url=Classes.php" );
	}
	private function setText($conText) {
		$this->text = $conText;
	}
	public function getText() {
		return $this->text;
	}
}
class StudentProgress {
	
	protected $SI;
	
	function __construct($si) {
		$this->setSI($si);
	}
	
	function parseSemester($semester, $semNum, $db) {
		$pos = strpos ( $semester, ")" );
		$semesterArea = substr ( $semester, 0, $pos + 1 );
		$semesterArea = trim ( $semesterArea );
		$sem = "Semester " . $semNum . " " . $semesterArea;
		$semester = substr ( $semester, $pos + 2, strlen ( $semester ) );
		if ($pos = strpos ( $semester, "(" )) {
			echo "another bracket found in semester " . $semNum . " at position " . $pos;
			$specialCase = true;
			$semester = $this->electiveCase ( $sem, $semester, $db );
		}
		while ( ($pos = strpos ( $semester, "," )) !== false ) {
			$class = substr ( $semester, 0, $pos );
			$class = trim ( $class );
			$semester = substr ( $semester, $pos + 1, strlen ( $semester ) );
			if (empty ( $class )) {
				continue;
			} else {
				$userProg = "INSERT INTO userProgress (SI,semester, class, grade, required) VALUES ('$this->SI','$sem', '$class', '0', 'true')";
				if (mysqli_query ( $db, $userProg )) {
				} else {
					echo "Error: " . $userProg . mysqli_error ( $db ) . "<br>";
				}
			}
		}
		$class = trim ( $semester );
		// echo $class . "<br>";
		$userProg = "INSERT INTO userProgress (SI,semester, class, grade, required) VALUES ('$this->SI','$sem', '$class', '0', 'true')";
		if (mysqli_query ( $db, $userProg )) {
		} else {
			echo "Error: " . $userProg . mysqli_error ( $db ) . "<br>";
		}
		
		// echo "<br>";
	}
	function electiveCase($sem, $semester, $db) {
		$posComma = strpos ( $semester, "," );
		$posStart = strpos ( $semester, "(" );
		$posEnd = strpos ( $semester, ")" );
		if ($posComma === false) {
			echo "false";
			// start string from 0 to posEnd;
			$class = substr ( $semester, 0, $posEnd );
			$class = trim ( $class );
			echo "<br>";
			echo "class = " . $class . "<br>";
			// $semester = substr($semester, $posEnd + 1, strlen($semester));
			$semester = substr_replace ( $semester, "", 0, $posEnd + 1 );
			$userProg = "INSERT INTO userProgress (SI,semester, class, grade, required) VALUES ('$this->SI','$sem', '$class', '0', 'true')";
			if (mysqli_query ( $db, $userProg )) {
			} else {
				echo "Error: " . $userProg . mysqli_error ( $db ) . "<br>";
			}
		} else {
			// echo "true";
			$class = substr ( $semester, $posComma + 1, $posEnd );
			$class = trim ( $class );
			// echo "<br>";
			// echo "class = " . $class . "<br>";
			// $semester = substr($semester, $posEnd + 1, strlen($semester));
			$semester = substr_replace ( $semester, "", $posComma, $posEnd + 1 );
			$posComma = strpos ( $semester, "," );
			$semester = substr_replace ( $semester, "", $posComma - 1, $posComma );
			$userProg = "INSERT INTO userProgress (SI,semester, class, grade, required) VALUES ('$this->SI','$sem', '$class', '0','true')";
			// echo $userProg;
			if (mysqli_query ( $db, $userProg )) {
			} else {
				echo "Error: " . $userProg . mysqli_error ( $db ) . "<br>";
			}
		}
		// echo "sem = " . $semester . "<br>";
		
		return $semester;
	}
	function updateClassesCompleted($db) {
		$joinUpdate = "UPDATE userProgress\n" . "INNER JOIN CourseInfo\n" . "on CourseInfo.courseTitle = userProgress.class\n" . "SET userProgress.grade = CourseInfo.grade";
		if (mysqli_query ( $db, $joinUpdate )) {
		} else {
			echo "Error: " . $joinUpdate . mysqli_error ( $db ) . "<br>";
		}
	}
	function parseElectives($electives, $db) {
		// echo $electives;
		// \w{2,4}\s\d{3}
		$parseText = array (); // gets the start year
		if (preg_match_all ( '/\w{2,4}\s\d{3}/', $electives, $parseText )) {
		} else {
			echo "false";
		}
		$deleteEle = "DELETE FROM electivesTaken WHERE SI = ".$this->SI;
		mysqli_query ( $db, $deleteEle );
		for($i = 0; $i < count ( $parseText [0] ); $i ++) {
			$line = $parseText [0] [$i];
			// $update = "UPDATE userProgress WGST
			if ((strpos ( $line, 'PHIL' ) !== false) || (strpos ( $line, 'ENGL' ) !== false) || (strpos ( $line, 'RLST' ) !== false) || (strpos ( $line, 'WGST' ) !== false)) {
				$type = "Humanities Elective";
			} else {
				$type = "Approved Elective";
			}
			
			$electives = "INSERT INTO electivesTaken (SI,class, grade, type) VALUES ('$this->SI','$line', '0', '$type')";
			if (mysqli_query ( $db, $electives )) {
			} else {
				echo "Error: " . $electives . mysqli_error ( $db ) . "<br>";
			}
		}
	}
	function joinElectives($db) {
		$joinUpdate = "UPDATE electivesTaken\n" . "INNER JOIN CourseInfo\n" . "on CourseInfo.courseTitle = electivesTaken.class\n" . "SET electivesTaken.grade = CourseInfo.grade";
		if (mysqli_query ( $db, $joinUpdate )) {
		} else {
			echo "Error: " . $joinUpdate . mysqli_error ( $db ) . "<br>";
		}
		$update = "UPDATE userProgress uP, (
SELECT AAA.id, AAA.semester, AAA.class, AAA.grade, BBB.class as class1, BBB.grade as grade1, BBB.type FROM

(select id, semester, class, grade, 
( CASE class WHEN @curType THEN @curRow := @curRow + 1 ELSE @curRow := 1 AND @curType := class END ) + 1 AS rank 
from userProgress, (SELECT @curRow := 0, @curType := '') r ORDER BY class) AAA

INNER JOIN (
select electivesTakenId, class, grade, type, ( 
CASE type 
WHEN @curType 
THEN @curRow := @curRow + 1 
ELSE @curRow := 1 AND @curType := type END
) + 1 AS rank from electivesTaken ,( SELECT @curRow := 0, @curType := '') r
where grade <> '0'
ORDER BY type
)
BBB ON AAA.class = BBB.type AND AAA.rank = BBB.rank) i
SET uP.grade = i.grade1, uP.class = CONCAT(i.class1, ' - ', i.type) where uP.id = i.id";
		
		if (mysqli_query ( $db, $update )) {
		} else {
			echo "Error: " . $update . mysqli_error ( $db ) . "<br>";
		}
	}
	
	public function getClassNotInProgress($db) {
		$getNotIn = "SELECT CourseInfo.courseTitle from CourseInfo LEFT JOIN userProgress ON userProgress.class LIKE CONCAT('%', CourseInfo.courseTitle, '%') WHERE userProgress.class IS NULL ";// AND userProgress.SI = ".$this->SI;
		
		$result = mysqli_query ( $db, $getNotIn );
		return $result;
	}
	public function updateRequired($db){
		$uReq = "UPDATE userProgress SET userProgress.required = 'false' where userProgress.class LIKE '%Elective%' ";
		if (mysqli_query ( $db, $uReq )) {
		} else {
			echo "Error: " . $uReq . mysqli_error ( $db ) . "<br>";
		}
	}
	private function setSI($si){
		$this->SI= $si;
	}
}


class Logout extends GotEnggGeneric{

	//private $SI;
	//protected $db = new dbConnection

	//$connection = new dbConnection ();
	//$connection->connect ();
	//$db = $connection->getDb ();
	
	
	function __construct($si){
	$this->setSI($si);		
	$this->getDb();
	}
	
	public function logout(){
	
		$sql ="SELECT * FROM StuInfo WHERE SI = " .$this->SI;
		$results = mysqli_query($this->db, $sql);
		
		while ($rows = mysqli_fetch_assoc($results)){
		print_r($rows); 
		}
		
	}
	
	//Sets the SI that needs to be logged out
	/*private function setSI($si){
		
		$this->SI=$si;
	}*/
	
	
}



// This is a genneric abstract class that will be inherited by all the classes that require a DB and an SI number
abstract class GotEnggGeneric {
	
	protected $db;
	protected $SI;
	
	
	//abstract 
	protected function setSI($si){
		
		$this->SI =$si;
	}
	// sets the instace of the db
	//abstract 
	protected function getDb(){
		
		$connection = new dbConnection ();
		$connection->connect ();
		$this->db = $connection->getDb ();
	}
}





?>