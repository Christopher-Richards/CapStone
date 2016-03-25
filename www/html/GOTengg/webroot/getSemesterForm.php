<?php
function loadClassSchedList($semester) {
	$classSchedForm = array ();
	$dataFields = array (
			'p_term' => $semester,
			'p_calling_proc' => 'bwckschd.p_disp_dyn_sched' 
	);
	$url = 'https://banner.uregina.ca/prod/sct/bwckgens.p_proc_term_date';
	$result = listLoadingHelper ( $dataFields, $url );
	// echo $result;
	$options = performDomRequest ( $result, 'subj_id', 'option' );
	foreach ( $options as $option ) {
		$value = $option->getAttribute ( 'value' );
		$text = $option->textContent;
		$classSchedForm [] = $value;
	}
	print_r ( $classSchedForm );
	curl_close ( $curl_connection );
}
function grabRecentSemester() {
	$curl_connection = curl_init ();
	curl_setopt ( $curl_connection, CURLOPT_CONNECTTIMEOUT, 30 );
	curl_setopt ( $curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)" );
	curl_setopt ( $curl_connection, CURLOPT_URL, 'https://banner.uregina.ca/prod/sct/bwckschd.p_disp_dyn_sched' );
	curl_setopt ( $curl_connection, CURLOPT_RETURNTRANSFER, true );
	curl_setopt ( $curl_connection, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt ( $curl_connection, CURLOPT_FOLLOWLOCATION, 1 );
	// curl_setopt ( $curl_connection, CURLOPT_POSTFIELDS, http_build_query($data_fields) );
	
	// perform our request
	$result = curl_exec ( $curl_connection );
	// echo $result;
	$options = performDomRequest ( $result, 'term_input_id', 'option' );
	$array = array ();
	foreach ( $options as $option ) {
		$value = $option->getAttribute ( 'value' );
		$text = $option->textContent;
		$array [] = $value;
	}
	
	curl_close ( $curl_connection );
	return $array [1];
}
function loadPreReqsList($semester) {
	$preReqsForm = array ();
	$dataFields = array (
			'cat_term_in' => $semester,
			'call_proc_in' => 'bwckctlg.p_disp_dyn_ctlg' 
	);
	$url = 'https://banner.uregina.ca/prod/sct/bwckctlg.p_disp_cat_term_date';
	$result = listLoadingHelper ( $dataFields, $url );
	// echo $result;
	$options = performDomRequest ( $result, 'subj_id', 'option' );
	foreach ( $options as $option ) {
		$value = $option->getAttribute ( 'value' );
		$text = $option->textContent;
		$preReqsForm [] = $value;
	}
	echo "<br>";
	print_r ( $preReqsForm );
	curl_close ( $curl_connection );
}
function listLoadingHelper($dataFields, $url) {
	$curl_connection = curl_init ();
	curl_setopt ( $curl_connection, CURLOPT_CONNECTTIMEOUT, 30 );
	curl_setopt ( $curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)" );
	curl_setopt ( $curl_connection, CURLOPT_URL, $url );
	curl_setopt ( $curl_connection, CURLOPT_RETURNTRANSFER, true );
	curl_setopt ( $curl_connection, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt ( $curl_connection, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt ( $curl_connection, CURLOPT_POSTFIELDS, http_build_query ( $dataFields ) );
	
	// perform our request
	$result = curl_exec ( $curl_connection );
	
	return $result;
}
function performDomRequest($resultPage, $idName, $tagName) {
	$dom = new DOMDocument ();
	if ($dom->loadHTML ( $resultPage )) {
	} else {
	}
	$select = $dom->getElementById ( $idName );
	if ($select == null) {
	} else {

	}
	$options = $select->getElementsByTagName ( $tagName );
	if ($options->length == 0) {
	} else {
	}
	return $options;
}
$recentSemester = grabRecentSemester ();
loadClassSchedList ($recentSemester);
loadPreReqsList ($recentSemester);
?>