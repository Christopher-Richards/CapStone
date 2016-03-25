<?php
session_start ();

if ((include '/var/www/html/GOTengg/src/Model.php') == TRUE) {
	echo "correct model </br>";
}
$text = $_POST ["studenttext"];

session_unset (); // unsets any previously set session variables /**********NEED TO CLEAR DB FIRST**************/

$hController = new HomeController ($text);

if ($_SESSION['SI']=$hController->getStudentId()){
	
	$hController->determineParser($_SESSION['SI']);
}
print_r($_SESSION);

//session_destroy (); // ***************** FOR LOGOUT ****************


determineParser ( $text );

/*
 * $admParse = new AdminParse($text);
 *
 * $admParse->parse();
 * $admParse->redirect();
 * if ((include '/var/www/html/GOTengg/src/Model2.php') == TRUE) {
 *
 * }
 * $text = $_POST["studenttext"];
 * $Parse = new Parse($text);
 *
 * $Parse->determineParser();
 * $Parse->redirect();
 */

/*
 * This class will be used to control the interaction with the home screen
 */
class HomeController {
	private $transcript;
	function __construct($text) {
		$this->setTranscript ( $text );
	}
	
	/**
	 * *************************************************************
	 * This function will check the text of the transcript to search for a student id
	 * If found , it will return the value, if not it will return null
	 */
	public function getStudentId() {
		
		preg_match ( '/\d{9}/', $this->transcript, $ID ); // this will get the SID of the student
		
		if (count($ID) >0){
		return $ID[0]; 
		}
		else 
			return null;
	}
	
	/**
	 * **************************************************************
	 * This Function determins if the transcript inserted was an admin or a student transcript
	 * The key difference is that the admin transcript contains the word "Term Code" within it
	 *
	 * Note curently there is no function to check if the rest of the info is contained
	 */
	public function determineParser($si) {
		if (strpos ( $this->transcript, "Term Code" )) {
			
			$admParse = new AdminParse ( $this->transcript,$si);
			$admParse->parse ();
			$admParse->redirect ();
		} else {
			
			$stuParse = new StudentParse ( $this->transcript,$si );
			$stuParse->studentParse ();
			$stuParse->redirect ();
		}
	}
	
	private function setTranscript($text) {
		$this->transcript = $text;
	}
}

?>