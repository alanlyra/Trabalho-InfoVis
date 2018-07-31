<?php
require_once 'system.php';
require_once 'checklogin.php';
saveCurrentURL();
?>

<html>
    <head>
        <meta charset="UTF-8">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link href="https://fonts.googleapis.com/css?family=Anton|Roboto:300,400,700" rel="stylesheet">
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
	<title>Transcritor da Defensoria Pública do Estado do Rio de Janeiro</title>     
        <link rel="stylesheet" type="text/css" href="/css/default.css"/>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    </head>




<?php


function showAudienceList() {

    $query_results=get_data('SELECT audience.audience_id, file_path, media_type FROM audience JOIN audience_media ON audience_media.audience_id=audience.audience_id order by audience.audience_id');

    $results_max = pg_num_rows($query_results);

    if  ($results_max>0) {
        while($result=pg_fetch_object($query_results)){

            $media_type='';
            if ($result->media_type=='Video') {
              $media_type='Video File';
            }
            else {
              $media_type='Audio File';
            }
            echo '<a href="#" onclick="playVideo(\''.$result->file_path.'\', 0, '.$result->audience_id.'); getFullTranscription('.$result->audience_id.',\''.$result->file_path.'\');" style="color:black;">&nbsp; Audiência '. $result->audience_id .'</a><br>';
        }
    }
}

    ?>





<body onload="javascript:onLoad()">

	<div class="site">
		<header class="l-cabecalho">
				
 				<div class="container">
<table align="right" style="padding-top: 7px;">
						<tr>		     
						    <td align=right class="menuitem">
							<?php echo $_SESSION['name']?> (<?php echo $_SESSION['email']?>)
						    </td>
						    <td align=right class="menuitem">
							<a href='login.php?action=logout' class="menuitem">Sair</a>
						    </td>
						</tr>
					    </table>
					<a href="/main.php" class="logo-defensoria">
					</a>
					
				<table width=100%>
				    
				    <tr class="menubar">
					<td width=100% style="background-color:#1abc9c">
					    <table>
						<tr>
						    <div class="dropdown">
				  <button class="dropbtn">Audiências</button>
				  <div class="dropdown-content">
					   <?php
					    showAudienceList();
					   ?>
				  </div>
		 		<a href='/search.php'>		
					<button class="dropbtn">Busca por palavras</button>
				</a>
				</div>
						</tr>
					    </table>
					</td>
				    </tr>
				</table>
 				</div>
		</header>

		<main>
			<header class="main-header">
			        <h2 class="titulo-pagina">
				   <span class="titulo-subsecao">Transcritor</span>
			        </h2>
				
			</header>
			<div class="container">
				
		
				<br>
				<table>
				<tr>
				   
				    <td width=470 valign=top>
					<h3 id="videoPanel" class="subtitle-settings" style="visibility: hidden;">
					    <span id="mediaName" class="subtitle-box"></span>
					</h3>
					<!-- Video Panel-->
					  <div id="playbackVideo"
					       style="top:0px;width:450px;display:none;">
					  <video id="video"
					    preload="auto"
					    width="450"
					    controls="controls"
					    />
					  </div>
				    </td>
				    <td valign=top>
					<h3 id="transcriptPanel" class="subtitle-settings" style="visibility: hidden;">
					    <span class="subtitle-box">Transcrição</span>
					</h3>
					<!-- Transcript Panel-->

					<div id="full_transcription" style="border:1;overflow-y:auto; height:400px;"></div>

				    </td>
				</tr>
				</table>


			</div>
		</main>

		<footer class="l-footer">
      	<span class="versao-sistema">  </span>
        <img src="images/logo-defensoria-reduzido.png?v=" alt="Logo da Defensoria Pública do Estado do Rio de Janeiro" class="logo-desfensoria">
    </footer>
	</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.bundle.min.js"></script>
</body>






<!--

  <div id="playbackVideo"
       style="position: fixed;
    bottom: 0;
    right: 25;
    width: 500px;display:none;">
  <video id="video"
    preload="auto"
    width="500"
    controls="controls"
    />
  </div>


-->
  <script>
	

  function playVideo(videoUrl, time, id) {
       myVideo=document.getElementById('video');

	document.getElementById("mediaName").innerHTML="Audiência " + id;
	document.getElementById("videoPanel").style.visibility = "visible";
	document.getElementById("transcriptPanel").style.visibility = "visible";



       var arr = document.getElementsByTagName("track");
       for (var i = 0, len = arr.length; i < len; i++) {
            myVideo.removeChild(arr[i]);
        }

       track = document.createElement("track");
       track.kind = "subtitles";
       track.label = "Subtitles";
       track.srclang = "en";
       track.src = "subtitles.php?id="+id;
       myVideo.appendChild(track);
       myVideo.textTracks[0].mode = "showing";
       console.log(myVideo.textTracks);

       document.getElementById('playbackVideo').style.display="block";

       myVideo.src = videoUrl;
       myVideo.addEventListener('loadedmetadata', function() {
         this.currentTime = time + <?=PLAY_OFFSET_NEGATIVE?>;
         this.play();
       });
  }

	
 function getFullTranscription(id, file) {
	$.ajax({
	  type: "GET",
	  url: 'get_transcription.php?id='+id+'&file='+file,
	  cache:false,
	  success: function(response)
	  {
	    $("#full_transcription").html(response);
	  } 
	});
 }

function onLoad(){

	//playVideo('/files/45/45.mp4', 0, 45);
	//getFullTranscription(45, '/files/45/45.mp4');

	if(!getParameterByName("id")){
	}	
	else{
		var url = window.location.href;
		var id = getParameterByName("id");
		var start_time = getParameterByName("start");
		var file_path = getParameterByName("file");
		//var start_time = url.searchParams.get("start");
		//var file_path = url.searchParams.get("file");

		playVideo(file_path, 0, id);
		getFullTranscription(id, file_path)

	}
	

}

function getParameterByName(name) {
    var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}

  </script>
    

</html>
