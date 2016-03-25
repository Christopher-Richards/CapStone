<?php
//include '/var/www/html/GOTengg/src/Model.php';

if (include('/var/www/html/GOTengg/src/Model/Model.php') == TRUE) {
	echo 'OK </br>';
}
if (include('/var/www/html/GOTengg/src/Model/Model2.php') == TRUE) {
	echo 'OK </br>';
}
if (include('/html/GOTengg/src/Model.php') == TRUE) {
	echo 'OK </br>';
}
if (include('/GOTengg/src/Model.php') == TRUE) {
	echo 'OK </br>';
}
if (include('/src/Model.php') == TRUE) {
	echo 'OK </br>';
}
if (include('Model.php') == TRUE) {
	echo 'OK </br>';
}


//include '/var/www/html/GOTengg/src/Model2.php';

if ($_SERVER["REQUEST_METHOD"] == "POST"){
$text = $_POST["studenttext"];
//$admParse = new AdminParse($text);
//$Parse = new Parse($text);
//echo $text;

//$Parse->determineParser();
//$Parse->redirect();

//$admParse->parse();
//$admParse->redirect();

}
?>