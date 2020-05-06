<?php
include("vars.php");

$system = $_GET['system'];

$xml = $databases_dir.'/'.$system.'/'.$system.'.xml';
$img_dir = $wheels_dir.'/'.$system.'/';
$vid_dir = $videos_dir.'/'.$system.'/';

if ($system != '') { $pageTitle = ' - '.$system; } else { $pageTitle = ''; }
?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Wheel Galley<?php echo $pageTitle; ?></title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script src="jq.js"></script>
<script src="scrollto.js"></script>
</head>

<body>
<div id="mainContent">
<div id="menu">
<div id="menu_icon">&#9776;</div>
<div id="menu_list">
<?php

$systems = simplexml_load_file($databases_dir.'/Main Menu/Main Menu.xml');
$systems_list = array();
foreach($systems as $sys) {
	$name = $sys[0]['name'];
	if (!$sys[0]['enabled']) {
		$systems_list[] = array ('name' => "$name");
	}
}
asort($systems_list);
foreach($systems_list as $sys) {
	echo "\t".'<a href="?system='.$sys['name'].'">'."\n\t\t".'<img class="menu" src="'.$wheels_dir.'/Main Menu/'.$sys['name'].'.png" title="'.$sys['name'].'" />'."\n\t".'</a>'."\n";			
}
?>
</div>
</div>
<?php
if ($system != '') {	
	$count_img = $wheels_dir.'/Main Menu/'.$system.'.png';
	echo "\n".'<div class="count"><img class="shadowed" id="system_header" src="'.$count_img.'"/></div>'."\n\n";
}	
?>
<div id="galley">
<?php

if ($system != '') {	
	
	$games = simplexml_load_file($xml);

	$game_list = array();

	foreach($games as $game) {
		$game_list[] = array ('name' => "$game->description", 'code' => $game[0]['name'], 'date' => "$game->year", 'genre' => "$game->genre", 'firm' => "$game->manufacturer");
	}
	unset ($game_list[0]);

	$images = scandir($img_dir);
	$count = 0;
	

	$replace = '';		
	$prev_game = '';
	$prev_letter = '';
	foreach($game_list as $game) {
		$name = preg_replace("/\s\([^)]+\)/", "", $game['name']);
		$letter = strtolower(substr($name,0,1));
		if ($name != $prev_game) {
			$file = str_replace("'", "%27", $game['code']);
			$desc = $name.' ('.$game['genre'].')'.'&#10;'.$game['date'].' - '.$game['firm'];
			$desc2 = '<span>'.$name.'</span><br/>'.$game['date'].' - '.$game['firm'];
			$desc2 = str_replace("'", "%27", $desc2);
			$desc2 = str_replace(" ", "&nbsp;", $desc2);
			$idscroll = '';
			if ($letter != $prev_letter) {
				$idscroll = 'id="anch_'.$letter.'" ';
				$prev_letter = $letter;
			}			
			echo "\t".'<div '.$idscroll.'title="'.$desc.'" class="galley_c" onclick="lightbox_open(\''.$vid_dir.$file.'.mp4\', \''.$desc2.'\');">'."\n\t\t".'<img class="galley" src="'.$img_dir.$file.'.png" />'."\n\t".'</div>'."\n";
			$prev_game = $name;
			$count++;
		}
	}
}
?>
</div>
</div>
<div id="light" onClick="lightbox_close();">
	<video id="VisaChipCardVideo" loop >
		<source src="" type="video/mp4">
	</video>
	<div id="videoDesc"></div>
</div>

<div id="fade" onClick="lightbox_close();"></div>

<script type="text/javascript">
<?php
if ($count != 0) {
	echo "$('.count').append('<br/>'+".$count."+' ".$str100_titles."');";
}
?>
var lightBoxVideo = document.getElementById("VisaChipCardVideo");

$( document ).ready(resizeV);

$(window).resize(resizeV);


function resizeV() {

	var viewsize = $(window).width();
	
	if (viewsize<=800) {
		var divide=3;
		$('#galley').css("grid-template-columns", "auto auto auto");	
	} else if (viewsize<=1900) {
		var divide=4;
		$('#galley').css("grid-template-columns", " auto auto auto auto");	
	} else if (viewsize>=2000) {
		var divide=Math.round(viewsize/600);
		var str='';
		for (i=0; i<=divide; i++) {
			str = str+' auto';
		}
	$('#galley').css("grid-template-columns", str);	
	}
	var gallsize = Math.round((viewsize/divide)-40);
	if (gallsize>=400) {gallsize=400;}
	$('.galley_c').css("height", gallsize);
	$('.galley').css("width", gallsize);



}

$('#menu_icon').click(function() {
	lightbox_close();	
	$('#fade').css("opacity", "0").css("display", "block");
	$('#menu_icon').hide();
	$('#menu').css("background-color", "rgba(0,0,0,.9)");
	$('#menu_list').show();     
});

$(document).click(function(event) { 
  $target = $(event.target);
  if($target.attr('id')!='menu_icon' && $('#menu_list').is(":visible")) {
		$('#fade').css("opacity", "0").css("display", "none");
		$('#menu_icon').show();
		$('#menu').css("background-color", "transparent");
		$('#menu_list').hide();		
  }  
});

function lightbox_open(url, desc) {
	$('#fade').css("display", "block");
	desc = desc.replace('%27', "&apos;");
	var h = window.innerHeight;
	var light = document.getElementById("light");
	
	imgUrl = url.replace('mp4', 'png');
	imgUrl = imgUrl.replace('<?php echo $videos_dir; ?>', '<?php echo $wheels_dir; ?>');

	$('#videoDesc').append('<img id="videoImg" src="'+imgUrl+'" />');
	$('#fade').css("opacity", ".7");
	$('#light').css("opacity", "1");
	$('#menu').css("z-index", "800");
	$('#mainContent').css({'filter':'blur(10px)'});	

	lightBoxVideo.src = url;
	lightBoxVideo.volume = 0.2;
	lightBoxVideo.autoplay = true;

	$('#VisaChipCardVideo').css("max-height", h*.8);
	
	$('#light').css("display", "block").css("top", "50%");	
	$('#videoDesc').append('<p>'+desc+'</p>');
	var img = $("#videoImg").height();
	var h3 = (img/2)+35;
	$('#videoDesc p').css("transform", "translateY(-"+h3+"px");
}

function lightbox_close() {

	$('#VisaChipCardVideo').attr("src","");
	$('#VisaChipCardVideo').css("width", "100%");
	$('#light').css("opacity", "0").css("display", "none");
	$('#fade').css("opacity", "0").css("display", "none");
	$('#menu').css("z-index", "999");
	$('#mainContent').css({'filter':'none'});
	$('#videoDesc').empty();
	lightBoxVideo.pause();
}

$('body').bind('keypress', function(e) {
	var letter = 'anch_'+String.fromCharCode(e.keyCode);
	$.scrollTo(document.getElementById(letter),1200,{offset:-145,easing:'easeInOutQuint'});
});
$.easing.easeInOutQuint = function (x, t, b, c, d) {
	if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
	return c/2*((t-=2)*t*t*t*t + 2) + b;
};
</script>
<svg height="0" xmlns="http://www.w3.org/2000/svg">
    <filter id="drop-shadow">
        <feGaussianBlur in="SourceAlpha" stdDeviation="2"/>
        <feOffset dx="4" dy="4" result="offsetblur"/>
        <feFlood flood-color="rgba(0,0,0,.5)"/>
        <feComposite in2="offsetblur" operator="in"/>
        <feMerge>
            <feMergeNode/>
            <feMergeNode in="SourceGraphic"/>
        </feMerge>
    </filter>
</svg>
</body>
</html>