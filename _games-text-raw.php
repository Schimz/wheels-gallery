<?php
include("vars.php");

?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Wheel Galley - Games Raw</title>
<link href="_games-text-raw.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
<script src="jq.js"></script>
<script src="scrollto.js"></script>
</head>

<body>
<div id="mainContent">	
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
	
	$count2 = 0;
	foreach($game_list as $game) {		
		$name = preg_replace("/\s\([^)]+\)/", "", $game['name']);

		if ($name == $prev_game) {
			$count2++;
		} else {
			$game_list_sorted[] = array ('name'=> $name, 'sys'=>$sys['name'],'code' => $game['code']);
			/*print_r($game);
			echo '<br>';*/
		}
		
		if ($name != $prev_game) {
			$prev_game = $name;
		}
	}
}

$col = array_column( $game_list_sorted, "name" );
array_multisort( $col, SORT_NATURAL | SORT_FLAG_CASE, $game_list_sorted );


$glength=count($game_list_sorted);
$prev_letter = '';
for ($i=0; $i<$glength; $i++) {
	$letter = strtolower(substr($game_list_sorted[$i]['name'],0,1));
	$idscroll = '';
	if ($letter != $prev_letter) {
		$idscroll = ' id="anch_'.$letter.'"';
		$prev_letter = $letter;
	}
	$game_list_sorted[$i]['code'] = str_replace("'", "%27", $game_list_sorted[$i]['code']);
	$file = $_VIDEOS_DIR.'/'.$game_list_sorted[$i]['sys']."/".$game_list_sorted[$i][code];
	echo "\r".'<span'.$idscroll.' onclick="lightbox_open(\''.$file.'.mp4\');">'.$game_list_sorted[$i]['name'].'</span> ('.$game_list_sorted[$i]['sys'].')<br>';

}
echo '<br><span class="red">'.$i.'</span><br>';

?>
</div>
<div id="light" onClick="lightbox_close();">
	<video id="VisaChipCardVideo" loop >
		<source src="" type="video/mp4">
	</video>
</div>

<div id="fade" onClick="lightbox_close();"></div>
<script type="text/javascript">

var lightBoxVideo = document.getElementById("VisaChipCardVideo");


function lightbox_open(url) {
	url = url.replace("%27","'");
	
	$('#fade').css("display", "block");
	var h = window.innerHeight;
	var light = document.getElementById("light");
	
	$('#fade').css("opacity", ".7");
	$('#light').css("opacity", "1");
	$('#mainContent').css({'filter':'blur(10px)'});	

	lightBoxVideo.src = url;
	lightBoxVideo.volume = 0.3;
	lightBoxVideo.autoplay = true;

	$('#VisaChipCardVideo').css("max-height", h*.8);
	
	$('#light').css("display", "block").css("top", "50%");	
}

function lightbox_close() {

	$('#VisaChipCardVideo').attr("src","");
	$('#VisaChipCardVideo').css("width", "100%");
	$('#light').css("opacity", "0").css("display", "none");
	$('#fade').css("opacity", "0").css("display", "none");
	$('#mainContent').css({'filter':'none'});
	lightBoxVideo.pause();
}

$('body').bind('keypress', function(e) {
	var letter = 'anch_'+String.fromCharCode(e.keyCode);
	$.scrollTo(document.getElementById(letter),1200,{offset:0,easing:'easeInOutQuint'});
});
$.easing.easeInOutQuint = function (x, t, b, c, d) {
	if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
	return c/2*((t-=2)*t*t*t*t + 2) + b;
};


</script>

</body>
</html>