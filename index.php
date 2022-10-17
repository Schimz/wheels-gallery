<?php
include("vars.php");

$system = $_GET['system'];

?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Systems</title>
<link href="index.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
<script src="jq.js"></script>
<script src="scrollto.js"></script>
</head>

<body>
<div class="count2"></div>
<div id="galley">
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
foreach($systems_list as $sys) {
	$xml = $_DATABASES_DIR.'/'.$sys[name].'/'.$sys[name].'.xml';
	
	$games = simplexml_load_file($xml);

	$game_list = array();

	foreach($games as $game) {
		if (!$game[0]['enabled']) {
			$game_list[] = array ('name' => "$game->description", 'code' => $game[0]['name'], 'date' => "$game->year", 'genre' => "$game->genre", 'firm' => "$game->manufacturer");
		}
	}
	unset ($game_list[0]);


	$count = 0;
	

	$replace = '';		
	$prev_game = '';
	$prev_letter = '';
	foreach($game_list as $game) {
		$name = preg_replace("/\s\([^)]+\)/", "", $game['name']);
		$letter = strtolower(substr($name,0,1));
		if ($name != $prev_game) {
			$prev_game = $name;
			$count++;
		}
	}
	
	$count_img = $_WHEELS_DIR.'/Main Menu/'.$sys[name].'.png';
	echo "\n".'  <a href="gallery.php?system='.$sys[name].'"><div class="galley_c"><img class="shadowed" src="'.$count_img.'"/><div class="count">'.$count.'</div></div></a>';	
	$total = $total+$count;
}
?>
</div>
<script type="text/javascript">
<?php
	echo "$('.count2').append(''+".$total."+' ".$_STR100_TITLES."');";
?>

$( document ).ready(resizeV);

$(window).resize(resizeV);


function resizeV() {

	var viewsize = $(window).width();
	if (viewsize<=500) {
		var divide=2;
		$('#galley').css("grid-template-columns", "50% 50%");
	} else if (viewsize<=800) {	
		var divide=3;
		$('#galley').css("grid-template-columns", "33% 33% 33%");	
	} else if (viewsize<=1900) {
		var divide=4;
		$('#galley').css("grid-template-columns", "25% 25% 25% 25%");
	} else if (viewsize<=2560) {
		var divide=5;
		$('#galley').css("grid-template-columns", "20% 20% 20% 20% 20%");	
	} else if (viewsize>=2560) {
		var divide=Math.round(viewsize/500);
		var str='';
		for (i=0; i<=divide; i++) {
			str = str+' auto';
		}
		$('#galley').css("grid-template-columns", str);	
	}
	var gallsize = Math.round((viewsize/divide)-40);
	if (gallsize>=400) {gallsize=400;}
	$('.galley_c').css("height", gallsize);
}

</script>
</body>
</html>