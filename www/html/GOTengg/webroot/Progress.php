<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>GOTengg</title>
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"
	integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ=="
	crossorigin="anonymous">
<style type="text/css">
.carousel-inner>.item>img, .carousel-inner>.item>a>img {
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
				<button type="button" class="navbar-toggle collapsed"
					data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">GOT-Engg</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse"
				id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
				<li><a href="index.html">Home</a></li>
					<li><a href="Classes.php">Classes</a></li>
					<li class="active"><a href="#">Progress through Program</a>
					<li><a href="NextSemester.php"> Next Semester </a>
					<li><a href="TPlanner.php"> Schedule Planner </a></li>
					<li><a href="SearchClasses.php"> Search Classes </a>
					<li> <a href="Schedules.php">Schedules</a></li>


				</ul>

			</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container-fluid -->
	</nav>

<?php
// include '/var/www/html/GOTengg/config/dbConnection.php';
// include '/var/www/html/GOTengg/src/Model.php';
if ((include '/var/www/html/GOTengg/src/Model.php') == TRUE) {
}
$connection = new dbConnection ();

$connection->connect ();
$db = $connection->getDb ();

$del = "DELETE FROM userProgress";
$result = mysqli_query ( $db, $del );
mysqli_free_result ( $result );
$sql = "SELECT * FROM StuInfo";
$result = mysqli_query ( $db, $sql );
echo "<br />";
echo "<br />";
echo "<br />";
echo "<br />";
echo "<div class='container-fluid'>";
echo "<div class='row'>";
echo "<div class='col-md-12'>";
echo "<div class='jumbotron'>";
echo "<h2>";
echo "Current Progress Through Program";
echo "</h2>";
echo "<p>";
while ( $row = mysqli_fetch_assoc ( $result ) ) { // Creates a loop to loop through results
	
	echo " " . $row ['SI'] . " - " . $row ['name'] . " - " . $row ['major'];
}

echo "</p>";
echo "<p>";
echo "<a class='btn btn-primary btn-large' href='#'>New Student?</a>";
echo "</p>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";

mysqli_free_result ( $result );
/*
 * echo "<div class='tab-content'>";
 * echo "<div class='tab-pane' id='panel-553231'>";
 * echo "<p>";
 * echo "I'm in Section 1.";
 * echo "</p>";
 * echo "</div>";
 * echo "<div class='tab-pane active' id='panel-754113'>"
 * echo "<p>";
 * echo "Howdy, I'm in Section 2.";
 * echo "</p>";
 * echo "</div>";
 * echo "</div>";
 */

// $sql = "SELECT * FROM Schedule";
$sql = "SELECT * FROM Schedule WHERE Schedule.program LIKE '%Software Systems Engineering%'";
// $sql = "SELECT * FROM Schedule where id = '50'";
$result = mysqli_query ( $db, $sql );
$row = mysqli_fetch_assoc ( $result );

$sem1 = $row ['semester1'];
$sem2 = $row ['semester2'];
$sem3 = $row ['semester3'];
$sem4 = $row ['semester4'];
$sem5 = $row ['semester5'];
$sem6 = $row ['semester6'];
$sem7 = $row ['semester7'];
$sem8 = $row ['semester8'];
$sem9 = $row ['semester9'];
$electives = $row ['electives'];
mysqli_free_result ( $result );
$stuProg = new StudentProgress ();
$stuProg->parseSemester ( $sem1, 1, $db );
$stuProg->parseSemester ( $sem2, 2, $db );
$stuProg->parseSemester ( $sem3, 3, $db );
$stuProg->parseSemester ( $sem4, 4, $db );
$stuProg->parseSemester ( $sem5, 5, $db );
$stuProg->parseSemester ( $sem6, 6, $db );
$stuProg->parseSemester ( $sem7, 7, $db );
$stuProg->parseSemester ( $sem8, 8, $db );
$stuProg->parseSemester ( $sem9, 9, $db );
$stuProg->updateClassesCompleted ( $db );
$stuProg->parseElectives ( $electives, $db );
$stuProg->joinElectives ( $db );
$stuProg->updateRequired( $db );
mysqli_free_result ( $result );

$sql = "SELECT * FROM userProgress";
$result = mysqli_query ( $db, $sql );
echo "<div class='row'>";
echo "<div class='col-md-2'>";
echo "</div>";
echo "<div class='col-md-8'>";
echo "<div style='overflow:auto;height:100%;'>";
echo "<table class='table'>";
echo "<thead>";
echo "<tr>";
echo "<th>";
echo "Semester";
echo "</th>";
echo "<th>";
echo "Course";
echo "</th>";
echo "<th>";
echo "Grade";
echo "</th>";
echo "</thead>";
echo "<tbody>";
while ( $row = mysqli_fetch_assoc ( $result ) ) { // Creates a loop to loop through results
	echo "<tr>" . "<td>" . $row ['semester'] . "</td>" . "<td>" . $row ['class'] . "</td><td>" . $row ['grade'] . "</td>" . "</tr>";
}
echo "</table>"; // Close the table in HTML
mysqli_free_result ( $result );
$result = $stuProg->getClassNotInProgress ( $db );
echo "</br>";
echo "<table class='table'>";
echo "<thead>";
echo "<tr>";
echo "<th>";
echo "Unaccounted for classes";
echo "</th>";
echo "</thead>";
echo "<tbody>";
while ( $row = mysqli_fetch_assoc ( $result ) ) { // Creates a loop to loop through results
	echo "<tr>" . "<td>" . $row ['courseTitle'] . "</td>" . "</tr>";
}
echo "</table>"; // Close the table in HTML

echo "</div>";
echo "</div>";
mysqli_free_result ( $result );
?>
</body>
</html>