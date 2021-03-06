<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>GOTengg</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
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
<script type="text/javascript">

$(function() {
$("#search_button").click(function() {
	
	//var searchString    = $("#coursename").val();
	// forming the queryString
	var data = $( "#coursename").serialize();

    
	if(data) {
           									 // ajax call
            $.ajax({
                type: "POST",
                url: "SearchResults.php",
                data: data,
                //dataType: "string",
                beforeSend: function(html) { // this happens before actual call
                    $("#results").html(''); 
                    $("#searchresults").show();
               
               },
               success: function(html){ // this happens after we get results
                    $("#results").show();
                    $("#results").append(html);
             }
                });    
               }
               return false;
});
});
$(document).ready(function() {
	  $("tbody.connectedSortable").sortable({
	    connectWith: ".connectedSortable",
	    helper: "clone",
	    cursor: "move",
	    zIndex: 99999,
	    receive: function(event, ui) {
	      /* here you can access the dragged row via ui.item
	         ui.item has been removed from the other table, and added to "this" table
	      */
	      var addedTo = $(this).closest("table.mytable"),
	        removedFrom = $("table.mytable").not(addedTo);
	      alert("The ajax should be called for adding to " + addedTo.attr("id") + " and removing from " + removedFrom.attr("id"));
	    }
	  });
	});
</script>

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
					<li><a href="Progress.php">Progress through Program</a>
					<li><a href="NextSemester.php"> Next Semester </a>
					<li class="active"><a href="TPlanner.php"> Schedule Planner </a></li>
					<li><a href="SearchClasses.php"> Search Classes </a>
					<li> <a href="Schedules.php">Schedules</a></li>

				</ul>

			</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container-fluid -->
	</nav>
<br />
	<br />
	<br />
	<br />
	<div class='container-fluid'>
		<div class='row'>
			<div class='col-md-12'>
				<div class='jumbotron'>
					<h2>Plan Your Schedule</h2>

				</div>
			</div>
		</div>
	</div>
<br>
<br>
<?php
// include '/var/www/html/GOTengg/config/dbConnection.php';
// include '/var/www/html/GOTengg/src/Model.php';
if ((include '/var/www/html/GOTengg/src/Model.php') == TRUE) {
}
$connection = new dbConnection ();

$connection->connect ();
$db = $connection->getDb ();


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
$sql = "select *, 
    (
    CASE 
        WHEN SuggestedSchedule.inProgress = 0 AND SuggestedSchedule.pass = 0 THEN 'Not Completed'
        WHEN SuggestedSchedule.inProgress = 1 AND SuggestedSchedule.pass = 0 THEN 'Currently Enrolled'
        WHEN SuggestedSchedule.inProgress = 0 AND SuggestedSchedule.pass = 1 THEN 'Completed'
    END) AS Status
 from SuggestedSchedule where SuggestedSchedule.unAccounted = 0 order by SuggestedSchedule.term desc";
// $sql = "SELECT * FROM Schedule where id = '50'";
$result = mysqli_query ( $db, $sql );
echo "<div class='row'>";
echo "<div class='col-md-1'>";
echo "</div>";
echo "<div class='col-md-4'>";
echo "<h1>";
echo "Planned Schedule"; 
echo "</h1>";
echo "<div style='overflow:auto;height:100%;'>";
echo "<table id = 'classTable' class='table'>";
echo "<thead>";
echo "<tr class='classTable'>";
echo "<th>";
echo "Course Name";
echo "</th>";
echo "<th>";
echo "Term";
echo "</th>";
echo "<th>";
echo "Status";
echo "</th>";
echo "</thead>";
echo "<tbody class='connectedSortable'>";
while ( $row = mysqli_fetch_assoc ( $result ) ) { // Creates a loop to loop through results
	echo "<tr class='classTable'>" . "<td>" . $row ['courseTitle'] . "</td>" . "<td>" . $row ['term'] . "</td><td>" . $row ['Status'] . "</td>" . "</tr>";
}
echo "</table>"; // Close the table in HTML
echo "</div>";
echo "</div>";
mysqli_free_result ( $result );
echo "<div class='col-md-4'>";
echo "<h1>";
echo "Search for classes";
echo "</h1>";
echo"<form role='form' action= 'SearchResults.php' method ='post'>";
echo"<input type='text' name='search' id='coursename'/>";
echo "<input class='btn btn-primary btn-large' type='submit' name='button'
		id='search_button' value='search'/>";
echo"</form>";
echo"<div id='searchresults'  >";
echo"<div style='overflow: auto; height: 100%;'>";
echo"<table class='table' id='results'>";
	
echo"</table>";
echo"</div>";
echo"</div>";
echo "</div>";
echo "<div class='col-md-3'>";
echo "<h1>";
echo "Unaccounted for classes"; 
echo "</h1>";
$sql = "SELECT *, ( CASE WHEN SuggestedSchedule.inProgress = 0 AND SuggestedSchedule.pass = 0 THEN 'Not Completed' WHEN SuggestedSchedule.inProgress = 1 AND SuggestedSchedule.pass = 0 THEN 'Currently Enrolled' WHEN SuggestedSchedule.inProgress = 0 AND SuggestedSchedule.pass = 1 THEN 'Completed' END) AS Status FROM SuggestedSchedule where SuggestedSchedule.unAccounted = 1 AND SuggestedSchedule.courseTitle NOT LIKE '%ENGG 0%'";
$result = mysqli_query ( $db, $sql );
echo "<div style='overflow:auto;height:100%;'>";
echo "<table id = 'unAccountedTable' class='table'>";
echo "<thead>";
echo "<tr class='unAccountedTable'>";
echo "<th>";
echo "Course Name";
echo "</th>";
echo "<th>";
echo "Term";
echo "</th>";
echo "<th>";
echo "Status";
echo "</th>";
echo "</thead>";
echo "<tbody class='connectedSortable'>";
while ( $row = mysqli_fetch_assoc ( $result ) ) { // Creates a loop to loop through results
	echo "<tr class='unAccountedTable'>" . "<td>" . $row ['courseTitle'] . "</td>" . "<td>" . $row ['term'] . "</td><td>" . $row ['Status'] . "</td>" . "</tr>";
}
echo "</table>"; // Close the table in HTML
$result = mysqli_query ( $db, $sql );
echo "</div>";
echo "</div>";
?>
</body>
</html>