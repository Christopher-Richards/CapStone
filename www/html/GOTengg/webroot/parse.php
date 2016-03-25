<?php

include '/var/www/html/GOTengg/config/dbConnection.php';

$connection = new dbConnection();

$db2 = $connection->connect();
//$connection->connect();
//$db2 = $connection->getDb();
//echo $db2;

$db = mysqli_connect("localhost", "root", "ense400", "StudentInfo") or die("could not connect to the database: Error " . mysqli_error($db));

$sql = "SELECT * FROM StuInfo";
$result = mysqli_query($db, $sql);
$numRows = mysqli_num_rows($result );

if($numRows > 0 ){
	//Already info in there, need to delete (probably caused by someone pressing the back button
$deleteStu = "DELETE FROM StuInfo";
mysqli_query($db, $deleteStu );
$deleteCo = "DELETE FROM CourseInfo";
mysqli_query($db, $deleteCo);
}
else{

}



$text      = $_POST["studenttext"];
$parseTextName = array(); // gets the start year  
preg_match_all('/(\d{9}.*)/', $text, $parseTextName);
$temp = $parseTextName[0][0];
$pos  = strpos($temp, "\t");
$id = substr($temp, 0, $pos);
$id = trim($id);

$temp = substr($temp, $pos + 1, strlen($temp));

$pos = strpos($temp, "\t");

$name = substr($temp, 0, $pos);
$temp = substr($temp, $pos + 1, strlen($temp));

$pos = strpos($temp, "\t");

$campus = substr($temp, 0, $pos);
$temp   = substr($temp, $pos + 1, strlen($temp));
$pos    = strpos($temp, "\t");

$college = substr($temp, 0, $pos);
$temp    = substr($temp, $pos + 1, strlen($temp));

$pos = strpos($temp, "\t");

$degree = substr($temp, 0, $pos);
$temp   = substr($temp, $pos + 1, strlen($temp));

$pos = strpos($temp, "\t");

$major = substr($temp, 0, $pos);
$temp  = substr($temp, $pos + 1, strlen($temp));


$level = $temp;

$UserInfo = "INSERT INTO StuInfo (SI, StudentString, name,startYear,major) VALUES ('$id', 'NA', '$name','0000','$major')";
if (mysqli_query($db, $UserInfo)) {
} else {
    echo "Error: " . $UserInfo . "<br>" . mysqli_error($conn);
}
$parseText = array(); // gets the start year  
preg_match_all('/(\d{6}(.*?)\t\w{2,4}\s*\d{1,3}).*/', $text, $parseText);
for ($i = 0; $i < count($parseText[1]); $i++) {
    $line = $parseText[0][$i];
    if (strpos($line, 'Registered') !== false) {
        //Currently taking
        $temp = $line;
        $pos  = strpos($temp, "\t");
        $code = substr($temp, 0, $pos);
        $code = trim($code);
        $temp = substr($temp, $pos + 1, strlen($temp));
        $pos  = strpos($temp, "\t");
        $subject = substr($temp, 0, $pos);
        $temp    = substr($temp, $pos + 1, strlen($temp));
        $pos = strpos($temp, "\t");
        $number = substr($temp, 0, $pos);
        $temp   = substr($temp, $pos + 1, strlen($temp));
        $pos = strpos($temp, "\t");
        $title = substr($temp, 0, $pos);
        $temp  = substr($temp, $pos + 1, strlen($temp));
        $pos = strpos($temp, "\t");
        $status = substr($temp, 0, $pos);
        $temp   = substr($temp, $pos + 1, strlen($temp));
        $pos = strpos($temp, "\t");
        $campus = substr($temp, 0, $pos);
        $temp   = substr($temp, $pos + 1, strlen($temp));
        $cHours = $temp;
$subject = trim($subject);
$number = trim($number);
$courseTitle = $subject . " ". $number;
$CourseInfo = "INSERT INTO CourseInfo (courseID, courseTitle, term,grade) VALUES ('$id', '$courseTitle', '$code','Not Completed')";
if (mysqli_query($db, $CourseInfo )) {
} else {
    echo "Error: " . $CourseInfo . "<br>" . mysqli_error($db);
}

    } else {
        $temp = $line;
        $pos  = strpos($temp, "\t");
        $code = substr($temp, 0, $pos);
        $code = trim($code);
        $temp = substr($temp, $pos + 1, strlen($temp));
        $pos  = strpos($temp, "\t");
        $subject = substr($temp, 0, $pos);
        $temp    = substr($temp, $pos + 1, strlen($temp));
        $pos = strpos($temp, "\t");
        $number = substr($temp, 0, $pos);
        $temp   = substr($temp, $pos + 1, strlen($temp));
        $pos = strpos($temp, "\t");
        $title = substr($temp, 0, $pos);
        $temp  = substr($temp, $pos + 1, strlen($temp));
        $pos = strpos($temp, "\t");
        $cHours = substr($temp, 0, $pos);
        $temp   = substr($temp, $pos + 1, strlen($temp));
        $grade = $temp;
        $subject = trim($subject);
$number = trim($number);
$courseTitle = $subject . " ". $number;
$CourseInfo = "INSERT INTO CourseInfo (courseID, courseTitle, term,grade) VALUES ('$id', '$courseTitle', '$code','$grade')";
if (mysqli_query($db, $CourseInfo )) {
} else {
    echo "Error: " . $CourseInfo . "<br>" . mysqli_error($db);
}

    }
}
mysqli_close($db);
header( "refresh:1;url=Classes.php" );

?>
