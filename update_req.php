<?php
// This script is used to update service request from the form
// and to place it into SAP ERP
include ("login_pm.php"); 
include("/webservice/sapconnector.php");
set_time_limit(0);
include ("functions.php"); 
//if(!$loggedin) echo "<script>window.location.replace('/pm/login.php');</script>";

class Request
	{
			public $BREAKDOWN;	     //   1
			public $ID_EQUIPMENT;    //  18
			public $ID_NOTIFTYPE;    //   2
			public $ID_REPORTER;	 //  12
			public $NOTIFDATE;
			public $NOTIFTIME;
			public $SERVICEMODE; 	 //   4
			public $SHORT_TEXT; 	 //  32 positions
			public $LONG_TEXT;  	 // 132 positions
	}
 
	if(isset($_REQUEST['id'])) $id		= $_REQUEST['id'];
	if(isset($_REQUEST['eq'])) $eq		= $_REQUEST['eq'];
	if(isset($_REQUEST['place'])) $place	= $_REQUEST['place'];
	if(isset($_REQUEST['desc'])) $description	= sanitizestring($_REQUEST['desc']);
	if(isset($_REQUEST['user'])) $user_name		= $_REQUEST['user'];
	
	//var_dump($_REQUEST);
		//$desc_txt=iconv('ASCII//TRANSLIT','UTF-8',$description);
		$desc_txt=substr($description,0,12);
		//var_dump($desc_txt);
		$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
		$db_server->set_charset("utf8");
		If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
		mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
			
		if (isset($id))		
			$textsql='UPDATE requests SET description= WHERE id="'.$id.'"';
		else
			$textsql='INSERT INTO requests
						(user_id,equipment_id,place_id,description)
						VALUES( 1,'.$eq.','.$place.',"'.$description.'")';
		//echo $textsql;				
		$answsql=mysqli_query($db_server,$textsql);
		if(!$answsql) die("Database UPDATE of requests TABLE failed: ".mysqli_error($db_server));
	$time_fact=getdate();
	// EXPORT to SAP ERP Section
	// Prepare request
	if((int)$time_fact['mon']<10)
		$time_fact['mon']='0'.$time_fact['mon'];
	if((int)$time_fact['minutes']<10)
		$time_fact['minutes']='0'.$time_fact['minutes'];
	if((int)$time_fact['seconds']<10)
		$time_fact['seconds']='0'.$time_fact['seconds'];
	if((int)$time_fact['hours']<10)
		$time_fact['hours']='0'.$time_fact['hours'];	
	$time_req=$time_fact['hours'].':'.$time_fact['minutes'].':'.$time_fact['seconds'];
	$date_req=$time_fact['year'].'-'.$time_fact['mon'].'-'.$time_fact['mday'];
	
	echo '<pre>';
	var_dump($time_req);
	var_dump($date_req);
	$req= new Request();
	$req->BREAKDOWN='X';
	$req->ID_EQUIPMENT='300000000';
	$req->ID_NOTIFTYPE='M2';
	$req->ID_REPORTER=$user_name;
	$req->NOTIFDATE=$date_req;
	$req->NOTIFTIME=$time_req;
	$req->SERVICEMODE='NT_C';
	$req->SHORT_TEXT=$desc_txt;
	$req->LONG_TEXT=$description;
	//var_dump($req);
	echo '</pre>';
	$sdorder_num=SAP_connector($req);
	
	//echo '<script>history.go(-2);</script>';	
	
mysqli_close($db_server);
?>