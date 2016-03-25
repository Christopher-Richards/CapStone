<?php
include '/var/www/html/GOTengg/src/Model.php';
$text = $_POST["studenttext"];
echo $text;
$admParse = new adminParse();

$admParse->parse($text);
//$admParse->redirect();

?>