<?php
$db = mysqli_connect("localhost", "root", "ense400", "StudentInfo") or die("could not connect to the database: Error " . mysqli_error($db));
$text      = $_POST["sched"];
$parseText = array(); // gets the start year  
preg_match_all('/\w{2,4}\s\d{3}/', $text, $parseText);

echo $temp;
for ($i = 0; $i < count($parseText[0]); $i++) {
    $line = $parseText[0][$i];
$class = "INSERT INTO classes (semester, class) VALUES ('9','$line')";
if (mysqli_query($db, $class)) {
} else {
    echo "Error: " . $class. "<br>" . mysqli_error($db);
}

}
?>