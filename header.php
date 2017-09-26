<?php // header.php
	session_start();
	?>
	<html lang="ru">
		<head>
			<script src="/pm/js/OSC.js"></script>
			<script src="/pm/js/menu.js"></script>
			<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
		<link rel="stylesheet" href="/pm/css/jquery.minical.plain.css" type="text/css">
			<link rel="stylesheet" type="text/css" href="/pm/css/style.css" />
			<!--[if lt IE 9]> 
			<script type="text/javascript" src="./js/html5.js"></script>
			<![endif]-->
			<!--<script type="text/javascript" src="./js/jquery.js"></script>-->
			<script src="/pm/js/jquery-3.1.1.js"></script>
			<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
			
			<script type="text/javascript" src="/pm/js/jquery.minical.plain.js"></script>
			<script type="text/javascript"  src="/pm/js/myFunctions.js"></script>
			<script type="text/javascript"  src="/pm/js/accordion.js"></script>
		
<?php
	include_once 'functions.php';
	
	if (isset($user))
	{
		unset($user);
	}
	$userstr = '';
	if (isset($_SESSION['user']))
	{
		$user = $_SESSION['user'];
		$loggedin = TRUE;
		$status = $_SESSION['status'];
		$userstr = " ($user)";
	}
	else $loggedin = TRUE; //FALSE;
	echo "<title>Maintenance</title>".
	"</head><body>";
	$status=0; // Delete it later on
	if ($loggedin)
	{
		if($status==0) //full access
		{
			
			echo "<div class='dropdown'>
				<button onclick='myFunction2()' class='dropbtn'>Заявки</button>
				<div id=\"myDropdown2\" class=\"dropdown-content\">
				<a href=\"show_reqs.php\">Показать Заявки</a>
				<a href=\"create_req.php\">Новая Заявка</a>
				</div>
			</div>
			";//<div class=\"userid\">Вы вошли в систему как: $userstr</div>
			
			echo '<hr>';
		}
		
	}
	
?>