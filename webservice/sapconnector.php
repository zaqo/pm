<?php
function SAP_connector($params)
{

	include("login_pm.php");
	ini_set("soap.wsdl_cache_enabled", "0");
	set_time_limit(0);
	$locale = 'ru';
	
	$client = new SoapClient($wsdlurl, array('login'=> $SAP_username,
											'password'=> $SAP_password,
											'trace'=>1)
							); 

	 // Формирование заголовков SOAP-запроса
	$client->__setSoapHeaders(
	array(
		new SoapHeader('API', 'user', $SAP_username, false),
		new SoapHeader('API', 'password', $SAP_password, false)
		)
	);


	// Выполнение запроса к серверу SAP ERP
	try
	{
		//$result = $client->ZsdOrderAviCrud($params);
		$result = $client->Z_PM_ALM_NOTIF_CRUD2($params);//ZPmAlmNotifCrud
	}
	catch(SoapFault $fault)
	{
	// <xmp> tag displays xml output in html
		echo 'Request : <br/><pre><xmp>',
		$client->__getLastRequest(),
		'</xmp></pre><br/><br/> Error Message : <br/>',
		$fault->getMessage();
	} 
	
	//обработчик ответа
	//var_dump($result);
	$order=SAP_response_handler($result);
	
	
	// Вывод запроса и ответа
	echo "Запрос:<pre>".htmlspecialchars($client->__getLastRequest()) ."</pre>";
	echo "Ответ:<pre>".htmlspecialchars($client->__getLastResponse())."</pre>";
	
	// Вывод отладочной информации в случае возникновения ошибки
	if (is_soap_fault($result)) 
	{ 
		echo("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring}, detail: {$result->detail})"); 
	}

	return $order;
}

function SAP_response_handler($Return2)
{

	$content='';
	// Building up the message content
	
	echo '<table><tr><th>PARAMETER</th><th>VALUE</th></tr>';
	
	$result=$Return2->RETURN2->item;
	//var_dump($result);
		$message=$result->MESSAGE;
			$V1=$result->MESSAGE_V1;
			$V2=$result->MESSAGE_V2;
			$V3=$result->MESSAGE_V3;
			$V4=$result->MESSAGE_V4;
			$system=$result->SYSTEM;
			$sid=$result->ID;
			$param=$result->PARAMETER;
			$type=$result->TYPE;
			$num=$result->NUMBER;
	
		echo "<tr><td colspan=\"2\" ><hr color=\"black\" ></td></tr>";		
		if ($result->TYPE=='E')
		{
			echo "<tr><td>RESULT:</td><td>ERROR</td></tr>";	
		
			echo "<tr><td>Message:</td><td>$message</td></tr>";
			echo "<tr><td>Number:</td><td>$num</td></tr>";
			echo "<tr><td>ID:</td><td>$sid</td></tr>";
			echo "<tr><td>Parameter #:</td><td>$param</td></tr>";
			echo "<tr><td>V1:</td><td>$V1</td></tr>";
			echo "<tr><td>V2:</td><td>$V2</td></tr>";
			echo "<tr><td>V3:</td><td>$V3</td></tr>";
			echo "<tr><td>V4:</td><td>$V4</td></tr>";
			echo "<tr><td>System:</td><td>$system</td></tr>";
		}
		else
		{
			echo "<tr><td>Message:</td><td>$message</td></tr>";
			echo "<tr><td>Number:</td><td>$num</td></tr>";
			echo "<tr><td>ID:</td><td>$sid</td></tr>";
			echo "<tr><td>Parameter #:</td><td>$param</td></tr>";
			echo "<tr><td>V1:</td><td>$V1</td></tr>";
			echo "<tr><td>V2:</td><td>$V2</td></tr>";
			echo "<tr><td>V3:</td><td>$V3</td></tr>";
			echo "<tr><td>V4:</td><td>$V4</td></tr>";
			echo "<tr><td>System:</td><td>$system</td></tr>";
		}
	
	echo '</table>';
	return $num;
}


/*
 post Service order to PM
 */

function SAP_export_SO($rec_id)
{
// NOT FINISHED YET
//return order num
//OR 0 - if failed
	include("login_pm.php");
	ini_set("soap.wsdl_cache_enabled", "0");
	
		//Setting up the object
		$flight_in= new Flight();
		$flight_out= new Flight();
		
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
	//1.	
		//  LOCATE data for the pair
			$textsql="SELECT in_id,out_id,sent_to_SAP FROM  flight_pairs WHERE id=$rec_id";
				
			$answsql=mysqli_query($db_server,$textsql);
				
			if(!$answsql) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			if (!$answsql->num_rows)
			{
				echo "WARNING: No flights found for a given ID in flight_pairs <br/>";
				return 0;
			}	
			$pair_data= mysqli_fetch_row($answsql);
				
			// CHECKING IF THE FLIGHT WAS ALREADY PROCESSED
			if($pair_data[2])
			{
				echo "WARNING: flight data for the pair=$rec_id has been already exported to SAP ERP! Process aborted.<br/>";
				return 0;
			}	
			
	//2.		
				//  SETTING UP Flight's Objects			
				//  LOCATE incoming flight data
			$in_id=$pair_data[0];
			$out_id=$pair_data[1];
			
			//a. APPLY PACKAGE 
			
			/* FOR DEBUIGGING
			echo '<pre>';
				var_dump($discount_in);
				var_dump($discount_out);
			echo '</pre>';
			*/
			if(!ApplyPackage($in_id))
							echo "WARNING: COULD NOT APPLY PACKAGE TO THE FLIGHT: $in_id - FAILED! <br/>";
			else
							echo "SUCCESS: APPLIED PACKAGE TO THE FLIGHT: $in_id ! <br/>";
			if(!ApplyPackage($out_id))
							echo "WARNING: COULD NOT APPLY PACKAGE TO THE FLIGHT: $out_id - FAILED! <br/>";
			else
							echo "SUCCESS: APPLIED PACKAGE TO THE FLIGHT: $out_id ! <br/>";
			// b.AND DISCOUNT
			$discount_in=ApplyDiscounts($in_id);
			$discount_out=ApplyDiscounts($out_id);			
			
			if(!$discount_in)
							echo "WARNING: NO DISCOUNTS FOR THE FLIGHT: $in_id  <br/>";
			else
							echo "SUCCESS: APPLIED DISCOUNT TO THE FLIGHT: $in_id !<br/>";
			if(!$discount_out)
							echo "WARNING: NO DISCOUNTS FOR THE FLIGHT: $out_id <br/>";
			else
							echo "SUCCESS: APPLIED DISCOUNT TO THE FLIGHT: $out_id !<br/>";
			
			
			$textsql="SELECT * FROM flights WHERE id=$in_id";
				
			$answsql1=mysqli_query($db_server,$textsql);	
			if(!$answsql1) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			
				$flight_data= mysqli_fetch_row($answsql1);
				
				//SETTING UP Incoming Flight's Object
				$flight_in->id=$in_id;
				$flight_in->id_NAV=$flight_data[1];
				$flight_in->flight_date=$flight_data[2];
				$flight_in->flight_num=$flight_data[3];
				$flight_in->direction=$flight_data[4];
				$flight_in->plane_id=$flight_data[7];
				$flight_in->flight_type=$flight_data[8];
				$flight_in->plane_type=$flight_data[20];
				$flight_in->plane_mow=$flight_data[9];
				
				$flight_in->passengers_adults=$flight_data[11];
				$flight_in->passengers_kids=$flight_data[12];
				$flight_in->customer=$flight_data[13];
				$flight_in->bill_to=$flight_data[14];
				$flight_in->plane_owner=$flight_data[15];
				$flight_in->flight_cat=$flight_data[19];
				$flight_in->time_fact=$flight_data[20];
				$flight_in->plane_type=$flight_data[21];
				
			
			// Bill Date is now a date of incoming flight
			$billdate=$flight_data[2];
		
			// Locate Airport IATA code
			$aportsql='SELECT code,domain FROM airports WHERE id="'.$flight_data[10].'"';	
			$answsql=mysqli_query($db_server,$aportsql);	
			if(!$answsql) die("Database SELECT in airports table failed: ".mysqli_error($db_server));	
			
			$aport= mysqli_fetch_row($answsql);
			if(isset($aport[0])) 
				$flight_in->airport=$aport[0];
			else 
				echo "ERROR: Airport CODE COULD NOT BE LOCATED!!! <br/>";
			
			// LOCATE CLASS OF AIRCRAFT
			// KEEP IN MIND aircrats TABLE NEEDS to be UPDATED regularly
			$aircraftsql='SELECT air_class FROM aircrafts WHERE reg_num="'.$flight_data[7].'"';	
			$answsql_air=mysqli_query($db_server,$aircraftsql);	
			if(!$answsql_air) die("Database SELECT in aircrafts table failed: ".mysqli_error($db_server));	
			
			$aircraft= mysqli_fetch_row($answsql_air);
			if(isset($aircraft[0])) 
				$flight_in->plane_class=$aircraft[0];
			else 
				echo "ERROR: Aircraft record COULD NOT BE LOCATED!!! <br/>";
		
			//  LOCATE all services relevant to the flight
			$textsqlin='SELECT service,quantity FROM  service_reg WHERE flight="'.$flight_in->id_NAV.'"';
			$answsql=mysqli_query($db_server,$textsqlin);
			if(!$answsql) die("Database SELECT in service_reg table failed: ".mysqli_error($db_server));
			$rows = $answsql->num_rows;
			for ($j=0; $j<$rows; $j++)
			{
				$row= mysqli_fetch_row($answsql);
				$flight_in->services[]=$row;
				
			}
			$services_count_in=count($flight_in->services);

	//3.
			// SETTING UP OUTGOING FLIGHT	
			//  LOCATE outgoing flight's data
			
			$textsqlout="SELECT * FROM flights WHERE id=$out_id";	
			$answsql2=mysqli_query($db_server,$textsqlout);	
			if(!$answsql2) die("Database SELECT TO flights table failed: ".mysqli_error($db_server));	
			$flight_data_out= mysqli_fetch_row($answsql2);
				
				//SETTING UP outgoing Flight's Object
				$flight_out->id=$out_id;
				$flight_out->id_NAV=$flight_data_out[1];
				$flight_out->flight_date=$flight_data_out[2];
				$flight_out->flight_num=$flight_data_out[3];
				$flight_out->direction=$flight_data_out[4];
				$flight_out->plane_id=$flight_data_out[7];
				$flight_out->flight_type=$flight_data_out[8];
				$flight_out->plane_mow=$flight_data_out[9];
				
				$flight_out->passengers_adults=$flight_data_out[11];
				$flight_out->passengers_kids=$flight_data_out[12];
				$flight_out->customer=$flight_data_out[13];
				$flight_out->bill_to=$flight_data_out[14];
				$flight_out->plane_owner=$flight_data_out[15];
				$flight_out->flight_cat=$flight_data_out[19];
				$flight_out->time_fact=$flight_data_out[20];
				$flight_out->plane_type=$flight_data_out[21];
			
			// Locate Airport IATA code
			$aportsql='SELECT code,domain FROM airports WHERE id='.$flight_data_out[10];	
			$answsql=mysqli_query($db_server,$aportsql);
			if(!$answsql) die("Database SELECT in airports table failed: ".mysqli_error($db_server));	
	
			$aport= mysqli_fetch_row($answsql);
			if(isset($aport[0])) 
			{	
				$flight_out->airport=$aport[0];
				$destination_zone=$aport[1];  // <-- TAKEN BY THE DEPARTURE AIRPORT
			}
			else 
				echo "ERROR: Airport CODE COULD NOT BE LOCATED!!! <br/>";
		
			// LOCATE CLASS OF AIRCRAFT
			// KEEP IN MIND aircrats TABLE NEEDS to be UPDATED regularly
			$aircratsql='SELECT air_class FROM aircrafts WHERE reg_num="'.$flight_data[7].'"';	
			$answsql_air=mysqli_query($db_server,$aircraftsql);
				
			if(!$answsql_air) die("Database SELECT in aircrafts table failed: ".mysqli_error($db_server));	
			
			$aircraft= mysqli_fetch_row($answsql_air);
			if(isset($aircraft[0])) 
				$flight_out->plane_class=$aircraft[0];
			else 
				echo "ERROR: Aircraft record COULD NOT BE LOCATED!!! <br/>";
		
			//  LOCATE all services relevant to the flight
			$textsql='SELECT service,quantity FROM service_reg WHERE flight="'.$flight_out->id_NAV.'"';	
			$answsql=mysqli_query($db_server,$textsql);	
			if(!$answsql) die("Database SELECT in service_reg table failed: ".mysqli_error($db_server));	
			
			$rows = $answsql->num_rows;
			for ($j=0; $j<$rows; $j++)
			{
				$row= mysqli_fetch_row($answsql);
				$flight_out->services[]=$row;	
			}
			$services_count_out=count($flight_out->services);
	//4.	
			// Prepare request for SAP ERPclass Item
	
			$req = new Request();
			
			// Set up params
			$terminal='01'; // AIRPORT's terminal of departure
			$disc_type='ZK01'; //  Type of discount
			$disc_value=1;		// and it's value 
			$currency='';	// Currency in invoice
	
		
			// Preparing Items for INCOMING FLIGHT
			$items=new ItemList();
			for($it=0;$it<$services_count_in;$it++)
			{	
				$item1 = new Item();
				// 1. Item number
				$item_num=($it+1).'0';
				$item1->ITM_NUMBER=$item_num;
			
				// 2. Material code
				$service_id=$flight_in->services[$it][0];
			
			//2.1 LOCATE SAP SERVICE Id
			
				$servicesql='SELECT id_SAP,id FROM services WHERE id_NAV="'.$service_id.'"';	
				$answsql=mysqli_query($db_server,$servicesql);	
				if(!$answsql) die("Database SELECT in services table failed: ".mysqli_error($db_server));	

				$sap_service_id= mysqli_fetch_row($answsql);
			//echo "SERVICE ID: $service_id |--> SAP ID: $sap_service_id[0]<br/>  ";
				if (isset($sap_service_id[0]))
				{	
					//LOCATE AND APPLY DISCOUNT
					$service_id=$sap_service_id[1];
					if(array_key_exists($service_id,$discount_in)) $disc_value=$discount_in[$service_id];
					
					$item1->MATERIAL=$sap_service_id[0];
					$item1->TARGET_QTY=$flight_in->services[$it][1];
					$item1->COND_TYPE=$disc_type;
					$item1->COND_VALUE=$disc_value;
					$item1->CURRENCY=$currency;
					$item1->ID_AODB=$flight_in->id_NAV;
					$item1->ID_TERMINAL=$terminal;
					$item1->ID_AIRPORT=$flight_in->airport;
					$item1->ID_AIRCRAFTCLASS=$flight_in->plane_class;
				}
				else 
				{	
					echo "No SAP service ID located for service: $service_id  FLIGHT $out_id CANCELLED <br/> ";
					return 0;
				}
			//Item List section
			
				$items->item[$it] = $item1;
			}
		
		// AND ADDING UP ONES FOR THE OUTGOING
		$services_total=$services_count_in+$services_count_out;
		for($it_o=$it;$it_o<$services_total;$it_o++)
		{	
			$k=$it_o-$it;
			$item2 = new Item();
			// 1. Item number
			$item_num=($it_o+1).'0';
			$item2->ITM_NUMBER=$item_num;
			
			// 2. Material code
			$service_id=$flight_out->services[$k][0];
			
			//2.1 LOCATE SAP SERVICE Id
			
			$servicesql='SELECT id_SAP,id FROM services WHERE id_NAV="'.$service_id.'"';	
			$answsql=mysqli_query($db_server,$servicesql);	
			if(!$answsql) die("Database SELECT in services table failed: ".mysqli_error($db_server));	
			$sap_service_id= mysqli_fetch_row($answsql);
			
			if (isset($sap_service_id[0]))
			{	
				$service_id=$sap_service_id[1];
					if(array_key_exists($service_id,$discount_out)) 
						$disc_value=$discount_out[$service_id];
				
				$item2->MATERIAL=$sap_service_id[0];
				$item2->TARGET_QTY=$flight_out->services[$k][1];
				$item2->COND_TYPE=$disc_type;
				$item2->COND_VALUE=$disc_value;
				$item2->CURRENCY=$currency;
				$item2->ID_AODB=$flight_out->id_NAV;
				$item2->ID_TERMINAL=$terminal;
				$item2->ID_AIRPORT=$flight_out->airport;
				$item2->ID_AIRCRAFTCLASS=$flight_out->plane_class;
			}
			else 
			{	
				echo "No SAP service ID located for service: $service_id  FLIGHT $out_id CANCELLED <br/> ";
				return 0;
			}
			//Item List section
			
			$items->item[$it_o] = $item2;
		}
		$req->SALES_ITEMS_IN = $items;
	//5.
		// GENERAL SECTION (HEADER)
		// Locate Sales Contract ID
		// Currently the contract is selected by the payer (bill-to)
			$client_id=$flight_out->bill_to;  
			$contractsql='SELECT id_SAP FROM contracts WHERE id_NAV="'.$client_id.'" AND isValid=1';	
			$answsql=mysqli_query($db_server,$contractsql);
				
			if(!$answsql) die("Database SELECT in contracts table failed: ".mysqli_error($db_server));	
			
			$client_contract= mysqli_fetch_row($answsql);
			$contract_id=$client_contract[0];
			if (isset($client_contract[0]))
			{	
				$req->ID_SALESCONTRACT = $contract_id;
			}
			else 
			{
				echo "No contract defined for Client ID: $client_id  FLIGHT $out_id CANCELLED<br/>";
				return 0;
			}
		// Locate Customer ID for SAP ERP
		// it is going to be used as Owner
			
			$clientsql='SELECT id_SAP FROM clients WHERE id_NAV="'.$client_id.'" AND isValid=1';	
			$answsql=mysqli_query($db_server,$clientsql);
			if(!$answsql) die("Database SELECT in contracts table failed: ".mysqli_error($db_server));	
			
			$client_rec= mysqli_fetch_row($answsql);
			$client_id_SAP=$client_rec[0];
			if (isset($client_rec[0]))
			{	
				$req->ID_PLANEOWNER = $client_id_SAP;
			}
			else 
			{
				echo "No SAP ERP ID defined for Client ID: $client_id  => FLIGHT $flightid CANCELLED<br/>";
				return 0;
			}
				// General request section
				
			$service_mode='SO_C';	// CREATE
			if($flight_out->direction)
					$SalesDist='1';
			else
					$SalesDist='0';
			$req->SERVICEMODE = $service_mode; 		
			
			$req->FLIGHTDATEIN=$flight_in->flight_date;
			$req->FLIGHTDATEOUT=$flight_out->flight_date;
			$req->FLIGHTTIMEIN=$flight_in->time_fact;
			$req->FLIGHTTIMEOUT=$flight_out->time_fact;
			$req->ID_AIRCRAFTTYPEIN = $flight_in->plane_type;
			$req->ID_AIRCRAFTTYPEOUT = $flight_out->plane_type;
			$req->ID_NOOFFLIGHTIN=$flight_in->flight_num;
			$req->ID_NOOFFLIGHTOUT=$flight_out->flight_num;
			$req->ID_REGISTRATIONIN=$flight_in->plane_id;
			$req->ID_REGISTRATIONOUT=$flight_out->plane_id;
			$req->ID_NOOFFLIGHTOUT=$flight_out->flight_num;
			$req->ID_FLIGHTCATEGORY = $flight_out->flight_cat;
			$req->ID_FLIGHTTYPE = $flight_out->flight_type;
			$req->ID_AIRPORTCLASS = $destination_zone;// BUT ALTERNATIVELY IT COULD BE DONE VIA $destination_zone
			$req->RETURN2 = '';
			//$req->BAPIRET2 = '';
			
			$sdorder_num=SAP_connector($req);
			
			//if($sdorder_num)
			//echo "SUCCESS: order # $sdorder_num created! <br/>";
		
	mysqli_close($db_server);
	return $sdorder_num;
}			//END OF SAP_export_pair


?>