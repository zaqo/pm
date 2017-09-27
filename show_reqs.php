<?php require_once 'login_pm.php';

include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// LIST REQUESTS
		
			$check_in_mysql="SELECT requests.id,eq_reg.name,description,date_rec,users.name
							FROM requests
							INNER JOIN users ON requests.user_id=users.id
							INNER JOIN eq_reg ON requests.equipment_id=eq_reg.id_SAP							
							WHERE 1 ORDER BY requests.id ";
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("LOOKUP into requests TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$content.= '<table class="fullTab"><caption><b>Сервисные Заявки</b></caption><br>';
		$content.= '<tr><th>№ </th><th>Описание</th><th>Оборудование</th><th>Инициатор</th><th>Дата</th>
					</tr>';
		// Iterating through the array
		$counter=1;
		
		while( $row = mysqli_fetch_row( $answsqlcheck ))  
		{ 
				$rec_id=$row[0];
				
				$eq=$row[1];
				$req_txt=$row[2];
				$req_date=$row[3];
				$user_name=$row[4];
				
				
				$content.= "<tr><td>$counter</td>";
				//$content.= "<td><a href=\"edit_client.php?id=$rec_id\">$nav_id</a></td>";
				$content.= "<td>$req_txt</td>";
				//$content.= "<td>$place</td>";
				$content.= "<td>$eq</td>";
				$content.= "<td>$user_name</td>";
				$content.= "<td>$req_date</td>";				
				$content.= '</tr>';
				
			$counter+=1;
			
		}
		$content.= '</table>';
	Show_page($content);
	mysqli_close($db_server);
	
?>
	