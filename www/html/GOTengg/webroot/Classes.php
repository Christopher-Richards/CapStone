<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GOTengg</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
    <style type="text/css">
        .carousel-inner > .item > img,
        .carousel-inner > .item > a > img {
            width: 75%;
            margin: auto;
        }
    </style>
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">GOT-Engg</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        					<li><a href="index.html">Home</a></li>
					<li class="active"><a href="#">Classes</a></li>
					<li><a href="Progress.php">Progress through Program</a>
					<li><a href="NextSemester.php"> Next Semester </a>
					<li><a href="TPlanner.php"> Schedule Planner </a></li>
					<li><a href="SearchClasses.php"> Search Classes </a>
					<li> <a href="Schedules.php">Schedules</a></li>
					
				
                    </ul>

                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>

<?php
$db = mysqli_connect("localhost", "root", "ense400", "StudentInfo") or die("could not connect to the database: Error " . mysqli_error($db));
$sql = "SELECT * FROM StuInfo";
$result = mysqli_query($db, $sql);
	echo "<br />";
        echo "<br />";
	echo "<br />";
        echo "<br />";
echo "<div class='container-fluid'>";
	echo "<div class='row'>";
		echo "<div class='col-md-12'>";
			echo "<div class='jumbotron'>";
				echo "<h2>";
					echo "Showing Results for";
				echo "</h2>";
				echo "<p>";
					while($row = mysqli_fetch_assoc($result)){   //Creates a loop to loop through results

echo " " . $row['SI'] . " - " . $row['name'] . " - " . $row['major'];  
}

				echo "</p>";
				echo "<p>";
					echo "<a class='btn btn-primary btn-large' href='#'>New Student?</a>";
				echo "</p>";
			echo "</div>";
		echo "</div>";
	echo "</div>";
echo "</div>";

mysqli_free_result($result);

$sql = "SELECT * FROM CourseInfo";
$result = mysqli_query($db, $sql);

echo "<div class='row'>";
echo "<div class='col-md-2'>";
echo "</div>";
echo "<div class='col-md-8'>";
echo "<div style='overflow:auto;height:100%;'>";
echo "<table class='table'>";
echo "<thead>";
echo "<tr>";
echo "<th>";
echo "Course name";
echo "</th>";
echo "<th>";
echo "Term";
echo "</th>";
echo "<th>";
echo "Status";
echo "</th>";
echo "</thead>";
echo "<tbody>";
 // start a table tag in the HTML

while($row = mysqli_fetch_assoc($result)){   //Creates a loop to loop through results
echo "<tr>" . "<td>" . $row['courseTitle'] . "</td>" . "<td>" . $row['term'] . "</td><td>" . $row['grade'] . "</td>" . "</tr>";  //$row['index'] the index here is a field name
}

echo "</table>"; //Close the table in HTML
echo "</div>";
echo "</div>";
mysqli_free_result($result);


?>
</body>
</html>
