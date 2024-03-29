<?php
include("vars.php");

?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Wheel Galley - Games</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="jq.js"></script>
<script src="scrollto.js"></script>
<style>
.red {background-color:red;color:white;}
body {background-color:#bbb;font-family:Arial;}
a {font-size:16px;font-weight:300;}
table {background-color:black;}
span{display:inline-block;margin-left:10px;padding:2px 7px 2px 7px;font-size:20px;font-weight:700;}
.lnkSys {width:120px;}
.left {text-align:right;}
</style>
</head>

<body>
<?php
$total =0;
$systems = simplexml_load_file($_DATABASES_DIR.'/Main Menu/Main Menu.xml');
$systems_list = array();

foreach($systems as $sys) {
	$name = $sys[0]['name'];
	if (!$sys[0]['enabled']) {
		$systems_list[] = array ('name' => "$name");
	}
}

asort($systems_list);

$strSys = '';
foreach($systems_list as $sys) {
$strSys .= '<a href="#'.$sys[name].'"><img class="lnkSys" src="'.$_WHEELS_DIR.'/Main Menu/'.$sys[name].'.png"/></a>&nbsp;';
}


foreach($systems_list as $sys) {
	$xml = $_DATABASES_DIR.'/'.$sys[name].'/'.$sys[name].'.xml';
	
	$games = simplexml_load_file($xml);

	$game_list = array();

	foreach($games as $game) {
		$attr=$game->attributes();
		if (!$attr->enabled) {
		$game_list[] = array ('name' => "$game->description", 'code' => $game[0]['name'], 'date' => "$game->year", 'genre' => "$game->genre", 'firm' => "$game->manufacturer");
		}
	}
	unset ($game_list[0]);
	$replace = '';		
	$prev_game = '';
	$prev_letter = '';
	$count_img = $_WHEELS_DIR.'/Main Menu/'.$sys[name].'.png';
	echo "\n".'<br><a id="'.$sys[name].'"><table><tr><td><img src="'.$count_img.'"/></td><td>'.$strSys.'</td></tr></table>';	

	foreach($game_list as $game) {
		
		$name = preg_replace("/\s\([^)]+\)/", "", $game['name']);

		if ($name == $prev_game) {

			//echo '<span class="red">'.$name.'</span><br>';
					
		} else {
			
			echo '<span>'.$name.'</span> ('.$game['genre'].')<br>';
	
		}
			if ($name != $prev_game) {
		$prev_game = $name;

	}
	}
	echo '</table>';


}
?>


</body>
</html>