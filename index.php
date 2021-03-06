<?php

error_reporting(E_ALL | E_STRICT); 
define('ROOT', dirname(__FILE__));
//set url web 
$c_url = "https://vanectro.test";
$folder = 'compressed/video_story/';
$thumbs_dir = ROOT.DS.$folder;
$videos = [];

$files = array_diff(scandir(ROOT.DS.$folder), array('.', '..'));
foreach($files as $foto){
	if(strpos($foto, 'mp4') !== false){
		array_push($videos,$folder.$foto);	
	}
			
};	

if( $_POST["name"] ){
    if (!preg_match('/data:([^;]*);base64,(.*)/', $_POST['data'], $matches)) {
        die("error");
    }
	
    $file = str_replace($c_url."/".$folder,"",$_POST["name"]);
	$file = str_replace(".mp4",".jpg",$file);
    $data = $matches[2];
    $data = str_replace(' ','+',$data);
    $data = base64_decode($data);
    //lokasi gambar foto akan disimpan.
    file_put_contents($thumbs_dir.$file, $data);
    
    echo $thumbs_dir.$file;
    exit;
}

?>

<video id="video" src="<?php echo $videos[0];?>"  onerror="gagal(event)" controls="controls" preload="none"></video>

<script>
var videos = <?=json_encode($videos);?>;
var index = 0;
var video = document.getElementById('video');

let anux = 0;
video.addEventListener('canplay', function() {
	// set ambil gambar di detik ke sekian
	this.currentTime = Math.round(this.duration / 60);
	video.addEventListener('seeked', function() { getThumb(); }, false);
}, false);
	
function nextVideo(){
    if(videos[index]){
        video.src = '/'+videos[index];
        video.load();anux = 0;index++;
    }
};

function getThumb(){
	if(anux < 1){
		var filename = video.src;
		var w = video.videoWidth;
		var h = video.videoHeight;
		var canvas = document.createElement('canvas');

		canvas.width = w;
		canvas.height = h;
		var ctx = canvas.getContext('2d');
		ctx.drawImage(video, 0, 0, w, h);

		var data = canvas.toDataURL("image/jpg");
		var xmlhttp = new XMLHttpRequest;
		
		xmlhttp.onreadystatechange = function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){nextVideo();}
		};
		
		xmlhttp.open("POST", location.href, true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send('name='+encodeURIComponent(filename)+'&data='+data);
	}
	anux++;
};

function gagal(e) {
    console.log(e.target.error.code);
    nextVideo();
};

nextVideo();
</script>
