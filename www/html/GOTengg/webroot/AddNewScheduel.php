 <?php

$text="";

if ($_SERVER["REQUEST_METHOD"] == "POST"){

   $text=$_POST['scheduel'];        //gets the scheduel that was pasted
   $text=str_replace ("'" , "''" , $text); //replace ' so that sql can query

   $major=array();
   preg_match('/(SOFTWARE SYSTEMS ENGINEERING)|(INDUSTRIAL SYSTEMS ENGINEERING)|(PETROLEUM SYSTEMS ENGINEERING)|(ENVIORMENTAL SYSTEMS ENGINEERING)|(ELECTRONIC SYSTEMS ENGINEERING)/' ,$text, $major); //extract major from the scheduel
   //echo $major[0];

  $startYear=array();   // gets the start year
  preg_match('/(\d{4})/', $text, $startYear);
  //echo $startYear[0];

   $Semester=array();
   preg_match_all('/(Semester)\s.*(?:\))/', $text, $Semester);
      

   $SemesterString=preg_split('/(Semester).*(?:\))/', $text);  
   //print_r ($SemesterString);
   


   //This will loop through the semester array to extract the courses in each semester ( the first position in the array is useless)
  for ($i=1;$i<count($SemesterString);$i++)
  {
	$coursesArray=array();   // gets the courses for the given semester
  	preg_match_all('/\w{2,4}\s\d{3}(?!\d)/', $SemesterString[$i], $coursesArray);
        //print_r ($coursesArray);
	echo $Semester[0][$i-1];
	echo "</br>";
	$coursesString = implode(",",$coursesArray[0]);       //turns the elements of the array into a string
	echo $coursesString;
	echo "</br>";
	}

}

?>