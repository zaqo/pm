<?php 
require_once 'login_pm.php';

include ("header.php"); 
	
		
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		// EQUIPMENT
			$check_in_mysql='SELECT id,name FROM equipment WHERE 1';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into equipment TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$equipment_dd='<select name="eq" id="eq" class="dd" >';
		$equipment_dd.='<option value="0">---</option>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$equipment_dd.='<option value="'.$row[0].'">'.$row[1].'</option>';
		$equipment_dd.='</select>';		
		
		// PLACES
			$check_in_mysql='SELECT id,name FROM places WHERE 1';
					
					$answsqlcheck=mysqli_query($db_server,$check_in_mysql);
					if(!$answsqlcheck) die("SELECT into places TABLE failed: ".mysqli_error($db_server));
		// Top of the table
		$place_dd='<select name="place" id="pl" class="dd" >';
		$place_dd.='<option value="0">---</option>';
		while ($row = mysqli_fetch_row( $answsqlcheck ))
		$place_dd.='<option value="'.$row[0].'">'.$row[1].'</option>';
		$place_dd.='</select>';		
		
		$content.= '<form id="form" method=post action=update_req.php >
					<table><caption><b>Заявка</b></caption><br>
					<tr><th>Поле</th><th>Значение</th></tr>
					<tr><td>Местонахождение:</td><td>'.$place_dd.'</td></tr>
					<tr><td>Оборудование:</td><td>'.$equipment_dd.'</td></tr>
					<tr><td>Описание:</td><td><textarea rows="5" cols="45" name="desc" placeholder="Описание проблемы" ></textarea></td></tr>
					<tr><td colspan="2"><p>
					<input type="hidden" name="user" value="'.$user.'">
					<input type="submit" name="send" class="send" value="ВВОД"></p></td></tr>
					</table></form>';
		
		
	Show_page($content);
	
	mysqli_close($db_server);
	
?>
	