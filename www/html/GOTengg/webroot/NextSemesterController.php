<?php

//if ((include 'NextTerm.php') == TRUE) {}

//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );

require(__DIR__  . '/NextTerm.php');
//print_r (error_get_last());

switch ($_POST ["functionname"]) {

	case 'getAvailibleSemesters' :

		$semesters = new AvailibleSemesters();
		$semesters->getAvailibleSemesters();
		break;
	case 'getTerms' :
		$semester = new SemesterTerms();
		$semester->getTerms();
		break;
	case '' :
		$semester = $_POST ["semester"];
		$Nsemester = new NextSemester ( $semester );
		$Nsemester->optimalSemester ();
		break;
}
?>
