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
	$strSys .= '<a href="#'.$sys['name'].'"><img class="lnkSys" src="'.$_WHEELS_DIR.'/Main Menu/'.$sys['name'].'.png"/></a>&nbsp;';
}

$game_list_sorted = array();

foreach($systems_list as $sys) {
	$xml = $_DATABASES_DIR.'/'.$sys['name'].'/'.$sys['name'].'.xml';
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
	$count_img = $wheels_dir.'/Main Menu/'.$sys['name'].'.png';
	$count2 = 0;
	foreach($game_list as $game) {		
		$name = preg_replace("/\s\([^)]+\)/", "", $game['name']);

		if ($name == $prev_game) {
			$count2++;
		} else {
			$game_list_sorted[] = $name.' ('.$sys['name'].')';
		}
		
		if ($name != $prev_game) {
			$prev_game = $name;
		}
	}
}

sort ($game_list_sorted,SORT_NATURAL | SORT_FLAG_CASE);
$glength=count($game_list_sorted);
for ($i=0; $i<$glength; $i++) {
	$game = explode("(", $game_list_sorted[$i]);
	echo '<span>'.$game[0].'</span> ('.$game[1].'<br>';
}
echo '<br><span class="red">'.$i.'</span><br>';

?>


</body>
</html>