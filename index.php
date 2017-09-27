<?php 
require_once 'login_pm.php';
// INDEX
include ("header.php"); 	
		$content="";
		//Set up mySQL connection
			$db_server = mysqli_connect($db_hostname, $db_username,$db_password);
			$db_server->set_charset("utf8");
			If (!$db_server) die("Can not connect to a database!!".mysqli_connect_error($db_server));
			mysqli_select_db($db_server,$db_database)or die(mysqli_error($db_server));
		
		
		// LIST DEPARTMENTS
		
			$check_deps='SELECT id,name
							FROM department							
							WHERE direction_id=1';
					
					$answsql=mysqli_query($db_server,$check_deps);
					if(!$answsql) die("LOOKUP into department TABLE failed: ".mysqli_error($db_server));	
	
			$content.='<table class="transpTab"><caption>Выберите единицу оборудования:</caption>';
			
			$content.='<tr><td><div class="mini-menu">
						<ul>
							<li>
								<a href="http://">ПЕРЕЧЕНЬ ОБОРУДОВАНИЯ</a>
							</li>';
			$content_eq='<td>';	
			$content_dpt='';				
			while($row=mysqli_fetch_row($answsql))
			{
				$dpt_id=$row[0];
				$content.='<li class="sub">
                <a href="#" class="d'.$dpt_id.'">'.$row[1].'</a>';
				
				
				//CHECK GROUPS IN DEPARTMENT
				$check_groups='SELECT id
								FROM group_of_objects
											WHERE dpt_id='.$dpt_id.' AND area_id IS NULL';
					
				$answsql_dpt=mysqli_query($db_server,$check_groups);
				if(!$answsql_dpt) die("LOOKUP into eq_reg TABLE failed: ".mysqli_error($db_server));
							
				while($row_dpt=mysqli_fetch_row($answsql_dpt))
				{
							// BUILD A LIST OF OBJECTS IN THE GROUP
							$check_groups='SELECT id_SAP,name
											FROM eq_reg
											WHERE group_id='.$row_dpt[0];
					
							$answsql_eq=mysqli_query($db_server,$check_groups);
							if(!$answsql_eq) die("LOOKUP into eq_reg TABLE failed: ".mysqli_error($db_server));
							$content_dpt.='<div class="mydata" id="d'.$dpt_id.'">';
							while($row_eq=mysqli_fetch_row($answsql_eq))
							{
								$content_dpt.='<p><b><a href="/pm/create_req.php?id='.$row_eq[0].'">'.$row_eq[1].'</a></b></p>';
							}
							$content_dpt.='</div>';
				}
				
				//LIST AREAS
				
				$check_areas='SELECT area.id,area.name,group_of_objects.id
							FROM area
							LEFT JOIN group_of_objects
							ON (area.id=group_of_objects.area_id AND group_of_objects.dpt_id='.$dpt_id.'
								AND group_of_objects.direction_id=1)
							WHERE area.dpt_id='.$row[0];
					
					$answsql_area=mysqli_query($db_server,$check_areas);
					if(!$answsql_area) die("LOOKUP into areas TABLE failed: ".mysqli_error($db_server));
					$content.='<ul>';
					while($row_area=mysqli_fetch_row($answsql_area))
					{
						$gr_id=$row_area[2];
						$area_id=$row_area[0];
						$content.='<li class="sub"><a class="a'.$area_id.'" href="#">'.$row_area[1].'</a></li>';
						if(isset($row_area[2]))
						{
							
	
							// BUILD A LIST OF OBJECTS IN THE GROUP
							$check_groups='SELECT id_SAP,name
											FROM eq_reg
											WHERE group_id='.$gr_id;
					
							$answsql_eq=mysqli_query($db_server,$check_groups);
							if(!$answsql_eq) die("LOOKUP into eq_reg TABLE failed: ".mysqli_error($db_server));
							$content_eq.='<div class="mydata" id="a'.$dpt_id.'">';
							while($row_eq=mysqli_fetch_row($answsql_eq))
							{
								
								$content_eq.='<p><b><a href="/pm/create_req.php?id='.$row_eq[0].'">'.$row_eq[1].'</a></b></p>';
							}
							$content_eq.='</div>';
						}
					}
					$content.='</ul>';
				$content.='</li>';
			}
			
			/*
						<li class="sub">
                <a href="#">Пункт Меню - 1</a>
                <ul>
                   <li><a href="#">Ссылка - 1</a></li>
                   <li><a href="#">Ссылка - 2</a></li>
                   <li><a href="#">Ссылка - 3</a></li>
                   <li><a href="#">Ссылка - 4</a></li>
                   <li><a href="#">Ссылка - 5</a></li>
                   <li><a href="#">Ссылка - 6</a></li>
                   <li><a href="#">Ссылка - 7</a></li>
                   <li><a href="#">Ссылка - 8</a></li>
                </ul>
            </li>
            <li class="sub">
                <a href="#">Пункт Меню - 2</a>
                <ul>
                   <li><a href="#">Ссылка - 1</a></li>
                   <li><a href="#">Ссылка - 2</a></li>
                   <li><a href="#">Ссылка - 3</a></li>
                   <li><a href="#">Ссылка - 4</a></li>
                   <li><a href="#">Ссылка - 5</a></li>
                   <li><a href="#">Ссылка - 6</a></li>
                   <li><a href="#">Ссылка - 7</a></li>
                   <li><a href="#">Ссылка - 8</a></li>
                   <li><a href="#">Ссылка - 9</a></li>
                </ul>
            </li>
            <li class="sub">
                <a href="#">Пункт Меню - 3</a>
                <ul>
                    <li><a href="#">Ссылка - 1</a></li>
                    <li><a href="#">Ссылка - 2</a></li>
                    <li><a href="#">Ссылка - 3</a></li>
                    <li><a href="#">Ссылка - 4</a></li>
                    <li><a href="#">Ссылка - 5</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Контакты</a>
            </li>
        </ul>
    </div>';*/
		$content.='</div></td>';
		$content_eq.='</div>';
		$content_dpt.='</div></td></tr></table>';
			$content.=$content_eq;
			$content.=$content_dpt;
			Show_page($content);
	
?>
	