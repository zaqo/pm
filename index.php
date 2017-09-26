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
	
			$content.='<h1>Регистрация сервисных заявок</h1>';
			
			$content.='<div class="mini-menu">
						<ul>
							<li>
								<a href="http://">ПЕРЕЧЕНЬ ОБОРУДОВАНИЯ</a>
							</li>';
			while($row=mysqli_fetch_row($answsql))
			{
				$content.='<li class="sub">
                <a href="#">'.$row[1].'</a>';
				
				//LIST AREAS
				
				$check_areas='SELECT id,name
							FROM area							
							WHERE dpt_id='.$row[0];
					
					$answsql_area=mysqli_query($db_server,$check_areas);
					if(!$answsql_area) die("LOOKUP into areas TABLE failed: ".mysqli_error($db_server));
					$content.='<ul>';
					while($row_area=mysqli_fetch_row($answsql_area))
					{
						$content.='<li class="sub"><a href="#">'.$row_area[1].'</a></li>';
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
		$content.='</div>';
			
			Show_page($content);
	
?>
	