<?php
session_start();
//if ((include '/var/www/html/GOTengg/src/Model.php') == TRUE) {}
//if (( == TRUE)) {}


//ini_set( 'error_reporting', E_ALL );
//ini_set( 'display_errors', true );



		require(__DIR__  . '/NextTerm.php');
		//print_r (error_get_last());

switch ($_POST ["functionname"]) {
	
	case 'print' :
		//print_r (get_included_files ());
		if (isset($_SESSION['SI'])){
		$future_schedule = new FutureSchedule ($_SESSION['SI']);
		
		$future_schedule->printOptimalSchedule();
		//$planner->printTerm(201530);
		}
		$planner = new SchedulePlanner ();
		 $planner->printClasses ();
		break;
		
	 //default :
	 	//$planner = new FutureSchedule ();
	 //break;
	 
}


?>





























