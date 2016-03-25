<?php
class CurrentClasses {
	protected $mostRecentSemester;
	function __construct() {
		echo "in construct";
		$recentSemester = $this->grabRecentSemester ();
		$this->setSem($recentSemester);
		//$this->loadClassSchedList ( $recentSemester );
		//$this->loadPreReqsList ( $recentSemester );
	}
	public function actualCurrentClasses($semester) {
		$data_fields = array (
				'p_term' => $semester,
				'p_calling_proc' => 'bwckschd.p_disp_dyn_sched' 
		);
		
		$curl_connection = curl_init ();
		curl_setopt ( $curl_connection, CURLOPT_CONNECTTIMEOUT, 30 );
		curl_setopt ( $curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)" );
		curl_setopt ( $curl_connection, CURLOPT_URL, 'https://banner.uregina.ca/prod/sct/bwckgens.p_proc_term_date' );
		curl_setopt ( $curl_connection, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curl_connection, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $curl_connection, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $curl_connection, CURLOPT_POSTFIELDS, http_build_query ( $data_fields ) );
		
		// perform our request
		$result = curl_exec ( $curl_connection );
		
		// show information regarding the request
		print_r ( curl_getinfo ( $curl_connection ) );
		echo "<br>";
		echo curl_errno ( $curl_connection ) . '-' . curl_error ( $curl_connection );
		echo "<br>";
		// echo $result;
		
		echo "<br>";
		
		echo "BELOW IS ALL ENSE CLASSES FOR 201630";
		echo "<br>";
		$data_fields = array (
				'term_in' => '201630',
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
		$data = "term_in=201630&sel_subj=dummy&sel_day=dummy&sel_schd=dummy&sel_insm=dummy&sel_camp=dummy&sel_levl=dummy&sel_sess=dummy&sel_instr=dummy&sel_ptrm=dummy&sel_attr=dummy&sel_subj=ENEL&sel_crse=&sel_title=&sel_schd=%25&sel_insm=%25&sel_from_cred=&sel_to_cred=&sel_camp=%25&sel_levl=%25&sel_ptrm=%25&sel_instr=%25&begin_hh=0&begin_mi=0&begin_ap=a&end_hh=0&end_mi=0&end_ap=a";
		curl_setopt ( $curl_connection, CURLOPT_URL, 'https://banner.uregina.ca/prod/sct/bwckschd.p_get_crse_unsec' ); // use the URL that shows up in your <form action="...url..."> tag
		curl_setopt ( $curl_connection, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curl_connection, CURLOPT_POSTFIELDS, $data );
		$result = curl_exec ( $curl_connection );
		echo "<br>";
		echo $result;
		curl_close ( $ch );
		return $result;
	}
	public function clostCon($ch) {
		curl_close ( $ch );
	}
	public function currClassesAndReqs($semester) {
		$data_fields = array (
				'cat_term_in' => $semester,
				'call_proc_in' => 'bwckctlg.p_disp_dyn_ctlg' 
		);
		
		$curl_connection = curl_init ();
		curl_setopt ( $curl_connection, CURLOPT_CONNECTTIMEOUT, 30 );
		curl_setopt ( $curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)" );
		curl_setopt ( $curl_connection, CURLOPT_URL, 'https://banner.uregina.ca/prod/sct/bwckctlg.p_disp_cat_term_date' );
		curl_setopt ( $curl_connection, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curl_connection, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $curl_connection, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $curl_connection, CURLOPT_POSTFIELDS, http_build_query ( $data_fields ) );
		
		// perform our request
		$result = curl_exec ( $curl_connection );
		
		// show information regarding the request
		print_r ( curl_getinfo ( $curl_connection ) );
		echo "<br>";
		echo curl_errno ( $curl_connection ) . '-' . curl_error ( $curl_connection );
		echo "<br>";
		// echo $result;
		
		echo "<br>";
		
		echo "BELOW IS ALL ENSE CLASSES FOR 201630";
		echo "<br>";
		$data_fields = array (
				'term_in' => '201630',
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
		$data = "term_in=201630&call_proc_in=bwckctlg.p_disp_dyn_ctlg&sel_subj=dummy&sel_levl=dummy&sel_schd=dummy&sel_coll=dummy&sel_divs=dummy&sel_dept=dummy&sel_attr=dummy&sel_subj=ENEL&sel_crse_strt=&sel_crse_end=&sel_title=&sel_levl=%25&sel_schd=%25&sel_coll=%25&sel_divs=%25&sel_dept=%25&sel_from_cred=&sel_to_cred=&sel_attr=%25";
		curl_setopt ( $curl_connection, CURLOPT_URL, 'https://banner.uregina.ca/prod/sct/bwckctlg.p_display_courses' ); // use the URL that shows up in your <form action="...url..."> tag
		curl_setopt ( $curl_connection, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curl_connection, CURLOPT_POSTFIELDS, $data );
		$result = curl_exec ( $curl_connection );
		echo "<br>";
		echo $result;
		curl_close ( $ch );
		return $result;
	}
	public function loadClassSchedList($semester) {
		echo "in";
		$classSchedForm = array ();
		$dataFields = array (
				'p_term' => $semester,
				'p_calling_proc' => 'bwckschd.p_disp_dyn_sched' 
		);
		$url = 'https://banner.uregina.ca/prod/sct/bwckgens.p_proc_term_date';
		$result = $this->listLoadingHelper ( $dataFields, $url );
		// echo $result;
		$options = $this->performDomRequest ( $result, 'subj_id', 'option' );
		foreach ( $options as $option ) {
			$value = $option->getAttribute ( 'value' );
			$text = $option->textContent;
			$classSchedForm [] = $value;
		}
		//print_r ( $classSchedForm );
		curl_close ( $curl_connection );
		return $classSchedForm;
	}
	public function grabRecentSemester() {
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
		$options = $this->performDomRequest ( $result, 'term_input_id', 'option' );
		echo "done options";
		$array = array ();
		foreach ( $options as $option ) {
			$value = $option->getAttribute ( 'value' );
			$text = $option->textContent;
			$array [] = $value;
		}
		curl_close ( $curl_connection );
		return $array [1];
	}
	public function loadPreReqsList($semester) {
		$preReqsForm = array ();
		$dataFields = array (
				'cat_term_in' => $semester,
				'call_proc_in' => 'bwckctlg.p_disp_dyn_ctlg' 
		);
		$url = 'https://banner.uregina.ca/prod/sct/bwckctlg.p_disp_cat_term_date';
		$result = $this->listLoadingHelper ( $dataFields, $url );
		// echo $result;
		$options = $this->performDomRequest ( $result, 'subj_id', 'option' );
		foreach ( $options as $option ) {
			$value = $option->getAttribute ( 'value' );
			$text = $option->textContent;
			$preReqsForm [] = $value;
		}
		echo "<br>";
		//print_r ( $preReqsForm );
		curl_close ( $curl_connection );
		return $preReqsForm;
	}
	public function listLoadingHelper($dataFields, $url) {
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
	public function performDomRequest($resultPage, $idName, $tagName) {
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
	private function setSem($sem){
		$this->mostRecentSemester = $sem;
	}
	public function getSem(){
		return $this->mostRecentSemester;
	}
}
/*
 * $recentSemester = grabRecentSemester ();
 * loadClassSchedList ($recentSemester);
 * loadPreReqsList ($recentSemester);
 * $resultReqs = currClassesAndReqs();
 * $parseOfferedClasses = new ParseOfferedClasses();
 * $parseOfferedClasses->parseOfferedClass($resultReqs);
 * $result = actualCurrentClasses();
 * $futureClassParse = new FutureClassesParser();
 * $futureClassParse->parseFutureClasses($result);
 */

?>