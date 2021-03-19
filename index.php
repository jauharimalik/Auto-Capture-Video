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
    
    file_put_contents($thumbs_dir.$file, $data);
    
    print 'done '.$thumbs_dir.$file;
    exit;
}

?>

<video id="video" src="<?php echo $videos[0];?>"  onerror="failed(event)" controls="controls" preload="none"></video>

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
        console.log(index);
        console.log('loading: '+video.src);
        video.load();
		anux = 0;
        index++;
    }else{
        console.log('done');
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
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				console.log('saved');
				nextVideo();
			}
		};
		
		console.log('saving');
		xmlhttp.open("POST", location.href, true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send('name='+encodeURIComponent(filename)+'&data='+data);
	}
	anux++;
};

function failed(e) {
    switch (e.target.error.code) {
        case e.target.error.MEDIA_ERR_ABORTED:
            console.log('You aborted the video playback.');
        break;
        case e.target.error.MEDIA_ERR_NETWORK:
            console.log('A network error caused the video download to fail part-way.');
        break;
        case e.target.error.MEDIA_ERR_DECODE:
            console.log('The video playback was aborted due to a corruption problem or because the video used features your browser did not support.');
        break;
        case e.target.error.MEDIA_ERR_SRC_NOT_SUPPORTED:
            console.log('The video could not be loaded, either because the server or network failed or because the format is not supported.');
        break;
        default:
            console.log('An unknown error occurred.');
        break;
    };
    
    nextVideo();
};

nextVideo();
</script>
