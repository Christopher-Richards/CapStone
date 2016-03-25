<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="en">
<head>
    <title>Progress Bar</title>
</head>
<body>
<!-- Progress bar holder -->
<div id="progress" style="width:500px;border:1px solid #ccc;"></div>
<!-- Progress information -->
<div id="information" style="width"></div>
<?php
if ((include '/var/www/html/GOTengg/webroot/DynamicClassParse.php') == TRUE) {
}
if ((include '/var/www/html/GOTengg/webroot/ParseOfferedClasses.php') == TRUE) {
}
if ((include '/var/www/html/GOTengg/src/CurrentClasses.php') == TRUE) {
}
function actualCurrClasses($semester, $facultyName){
$data_fields = array('p_term' => $semester,
			'p_calling_proc' => 'bwckschd.p_disp_dyn_sched'
              ); 
 
$curl_connection = curl_init();
curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($curl_connection, CURLOPT_USERAGENT,
    "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
    curl_setopt($curl_connection, CURLOPT_URL, 'https://banner.uregina.ca/prod/sct/bwckgens.p_proc_term_date');
    curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt ( $curl_connection, CURLOPT_POSTFIELDS, http_build_query($data_fields) );
 
//perform our request
$result = curl_exec($curl_connection);
 
$data_fields = array('term_in' => '201630',
		'sel_to_cred' => '',
		'sel_title' => '',
		'sel_subj[0]' => '0',
		'sel_subj[1]' => '1',
	       'sel_sess' => 'dummy',
		'sel_schd' => '%',
		'sel_schd' => 'dummy',
		'sel_ptrm' => '%',
		'sel_ptrm' => 'dummy',
		'sel_levl' => '%',
		'sel_levl' => 'dummy',
		'sel_instr' => '%',
		'sel_instr' => 'dummy',
		'sel_insm' => '%',
		'sel_insm' => 'dummy',
		'sel_from_cred' => '',
		'sel_day' => 'dummy',
		'sel_crse' => '',
		'sel_camp' => '%',
		'sel_camp' => 'dummy',
		'sel_attr' => 'dummy',
		'end_mi' => '0',
		'end_hh' => '0',
		'end_ap' => 'a',
		'begin_mi' => '0',
		'begin_hh' => '0',
		'begin_ap' => 'a'
);
	$data = "term_in=".$semester."&sel_subj=dummy&sel_day=dummy&sel_schd=dummy&sel_insm=dummy&sel_camp=dummy&sel_levl=dummy&sel_sess=dummy&sel_instr=dummy&sel_ptrm=dummy&sel_attr=dummy&sel_subj=".$facultyName."&sel_crse=&sel_title=&sel_schd=%25&sel_insm=%25&sel_from_cred=&sel_to_cred=&sel_camp=%25&sel_levl=%25&sel_ptrm=%25&sel_instr=%25&begin_hh=0&begin_mi=0&begin_ap=a&end_hh=0&end_mi=0&end_ap=a";
 	curl_setopt($curl_connection, CURLOPT_URL, 'https://banner.uregina.ca/prod/sct/bwckschd.p_get_crse_unsec'); // use the URL that shows up in your <form action="...url..."> tag
	curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt ( $curl_connection, CURLOPT_POSTFIELDS,$data);
	$result = curl_exec($curl_connection);
	curl_close($ch);
return $result;
}

function clostCon($ch){
curl_close($ch);
}
function currClassesAndReqs($semester, $facultyName){
	$data_fields = array('cat_term_in' => $semester,
			'call_proc_in' => 'bwckctlg.p_disp_dyn_ctlg'
	);
	
	$curl_connection = curl_init();
	curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($curl_connection, CURLOPT_USERAGENT,
			"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
	curl_setopt($curl_connection, CURLOPT_URL, 'https://banner.uregina.ca/prod/sct/bwckctlg.p_disp_cat_term_date');
	curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt ( $curl_connection, CURLOPT_POSTFIELDS, http_build_query($data_fields) );
	
	//perform our request
	$result = curl_exec($curl_connection);
	
	//show information regarding the request
			$data_fields = array('term_in' => '201630',
					'sel_to_cred' => '',
					'sel_title' => '',
					'sel_subj[0]' => '0',
					'sel_subj[1]' => '1',
					'sel_sess' => 'dummy',
					'sel_schd' => '%',
					'sel_schd' => 'dummy',
					'sel_ptrm' => '%',
					'sel_ptrm' => 'dummy',
					'sel_levl' => '%',
					'sel_levl' => 'dummy',
					'sel_instr' => '%',
					'sel_instr' => 'dummy',
					'sel_insm' => '%',
					'sel_insm' => 'dummy',
					'sel_from_cred' => '',
					'sel_day' => 'dummy',
					'sel_crse' => '',
					'sel_camp' => '%',
					'sel_camp' => 'dummy',
					'sel_attr' => 'dummy',
					'end_mi' => '0',
					'end_hh' => '0',
					'end_ap' => 'a',
					'begin_mi' => '0',
					'begin_hh' => '0',
					'begin_ap' => 'a'
			);
$data = "term_in=".$semester."&call_proc_in=bwckctlg.p_disp_dyn_ctlg&sel_subj=dummy&sel_levl=dummy&sel_schd=dummy&sel_coll=dummy&sel_divs=dummy&sel_dept=dummy&sel_attr=dummy&sel_subj=".$facultyName."&sel_crse_strt=&sel_crse_end=&sel_title=&sel_levl=%25&sel_schd=%25&sel_coll=%25&sel_divs=%25&sel_dept=%25&sel_from_cred=&sel_to_cred=&sel_attr=%25";
curl_setopt($curl_connection, CURLOPT_URL, 'https://banner.uregina.ca/prod/sct/bwckctlg.p_display_courses'); // use the URL that shows up in your <form action="...url..."> tag
			curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt ( $curl_connection, CURLOPT_POSTFIELDS,$data);
			$result = curl_exec($curl_connection);
			curl_close($ch);
			return $result;
}
    $currClass = new CurrentClasses();
    $semester = $currClass->getSem();
    $preReqs = array();
    $cClass = array();
    $cClass = $currClass->loadClassSchedList($semester);
     $preReqs = $currClass->loadPreReqsList($semester);
     //print_r($cClass);
     //print_r($preReqs);
    $total = count($preReqs);
    echo $total;
$i = 0;
foreach ( $preReqs as $preReq ) {
$resultReqs = currClassesAndReqs($semester,$preReq);
$i = $i + 1;
    $percent = intval($i/$total * 100)."%";
    
    // Javascript for updating the progress bar and information
    echo '<script language="javascript">
    document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
    document.getElementById("information").innerHTML="'.$preReq.' currently processing. '.$percent.' Completed";
    </script>';
    

// This is for the buffer achieve the minimum size in order to flush data
    echo str_repeat(' ',1024*64);
    

// Send output to browser immediately
    flush();
    

// Sleep one second so we can see the delay
    sleep(1);
//echo "BELOW IS ALL ".$facultyName." CLASSES FOR ".$semester.".";
//ECHO "DONE PREREQS";
}
  //$parseOfferedClasses = new ParseOfferedClasses();
  //$parseOfferedClasses->parseOfferedClass($resultReqs);
  $result = actualCurrentClasses($semester,'ENEL');
  //$futureClassParse = new FutureClassesParser();
  //$futureClassParse->parseFutureClasses($result);
?>
</body>
</html>