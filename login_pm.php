<?php //login_avia.php
	
	//mySQL section
	$db_hostname= 'localhost';
	$db_database= 'maintenance';
	$db_username= 'php';
	$db_password= '12345';
	
	
	$systems=array();
	
	//SAP web service
	$SAP_username= 'PHP_SERVICE';
	$SAP_password= 'Service5#';
	
	
	//EP1 TESTED - IT WORKS!
	//$wsdlurl='http://srvr-186.local.newpulkovo.ru:8002/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/rfc/sap/zpm_alm_notif_crud3/001/4/4?sap-client=001';
	
	//ERD 
	$wsdlurl='http://srvr-185.local.newpulkovo.ru:8000/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/rfc/sap/zpm_alm_notif_crud/110/zpm_alm_notif_crud/zpm_alm_notif_crud?sap-client=110';
	
?>