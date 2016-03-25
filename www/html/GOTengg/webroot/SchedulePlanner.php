<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Search Classes</title>
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"
	integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ=="
	crossorigin="anonymous">
<style type="text/css">
.alists {
  display: inline-block;
  vertical-align: middle;
  list-style-type: none;
   float: none;
   width: 16%;
 }
 

</style>
<!--  
<script type="text/javascript"
	src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
-->
<script
	src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript">

// move target
//document.getElementById('target').appendChild(  document.getElementById('to_be_moved') )

$('document').ready(function(){


    $.ajax({
        type: "POST",
        url: "SchedulePlannerController.php",
        data: {functionname:'print'},
       success: function(html){ // this happens after we get results
          $("#classList").append(html);
     }

        });    	
})

function swapSemester(class_name,semester) {

	document.getElementById(semester).appendChild( document.getElementById(class_name) );
}
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
					<li><a href="Progress.php">Progress through Program</a></li>
					<li><a href="NextSemester.php"> Next Semester </a></li>
					<li class="active"><a href="#"> Schedule Planner </a></li>
					<li ><a href="SearchClasses.php">Search Classes</a></li>
					<li><a href="Schedules.php">Schedules</a></li>



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


	<div id='searchresults'>
		<div class='row'>
			<div class='col-md-1'></div>
			<div class='col-md-12'>
				<div style='overflow: auto; height: 100%;'>
					<ul>

						<div class="container" name="classList" id="classList"></div>

					</ul>
				</div>
			</div>
		</div>
	</div>
</body>
</html>




