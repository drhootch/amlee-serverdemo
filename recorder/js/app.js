
/* -----------------------------------------------------------------------------
	Onload 
	----------------------------------------------------------------------------- */
	
	var audio_filename = "";
		
	function removeAllRecord(){
		$('#recordingsList').html('');
		audio_filename = "";
		return;
	
	}

	
	jQuery( document ).ready(function() {
		$( "form" ).on( "submit", function(e) {
			
				$("#answer").html('');
				
				if(audio_filename == ""){
					$("#answer").html('<p class="error-message">يرجى رفع الملف الصوتي أولا ثم الضغط</p>');
					
				}else{
				
					$.ajax({
						type: 'POST',
						url: '/recorder/uploader.php',
						data: { 
							'dataform': "c3EQ1mU7j6Lb",
							'type': $('select[name="type"]').val(),
							'level': $('select[name="level"]').val(),
							'text': $('textarea[name="text"]').val(),
							'classification': $('input[name="classification"]').val(),
							'audio': audio_filename,
						
						},
						//dataType : 'json', // changing data type to json
						success: function (data) {
							$("#answer").html('<p class="notification-message">'+data+'</p>');
							//$.delay(5000);
							//location.reload();
							$("#recordingsList").html("");
							$('textarea[name="text"]').val("");
							audio_filename = "";
							
							    $("html, body").animate({
									scrollTop: 0
								}, 1000);
								
								
								
						},
						error:function(xhr, textStatus, thrownError, data){
							console.log("Error: " + thrownError);
							console.log("Error: " + textStatus);
						}
					});
					
					if($("#imagefile").val() != ""){
						//$('#upload').on('click', function() {
							var file_data = $('#imagefile').prop('files')[0];   
							var form_data = new FormData();                  
							form_data.append('imagefile', file_data);
							c
							alert(form_data);                             
							$.ajax({
								url: '/recorder/uploader.php', // <-- point to server-side PHP script 
								dataType: 'text',  // <-- what to expect back from the PHP script, if anything
								cache: false,
								contentType: false,
								processData: false,
								data: form_data,                         
								type: 'post',
								success: function(data){
									$("#answer").append('<p class="notification-message">'+data+'</p>');
								}
							 });
						//});
					}
				}
			    e.preventDefault();
		});
	});




/* -----------------------------------------------------------------------------
	 Audio processing: 
	----------------------------------------------------------------------------- */


//webkitURL is deprecated but nevertheless
URL = window.URL || window.webkitURL;

var gumStream; 						//stream from getUserMedia()
var rec; 							//Recorder.js object
var input; 							//MediaStreamAudioSourceNode we'll be recording

// shim for AudioContext when it's not avb. 
var AudioContext = window.AudioContext || window.webkitAudioContext;
var audioContext //audio context to help us record

var recordButton = document.getElementById("recordButton");
var stopButton = document.getElementById("stopButton");
var pauseButton = document.getElementById("pauseButton");

//add events to those 2 buttons
recordButton.addEventListener("click", startRecording);
stopButton.addEventListener("click", stopRecording);
pauseButton.addEventListener("click", pauseRecording);

function startRecording() {
	console.log("recordButton clicked");

	/*
		Simple constraints object, for more advanced audio features see
		https://addpipe.com/blog/audio-constraints-getusermedia/
	*/
    
    var constraints = { audio: true, video:false }

 	/*
    	Disable the record button until we get a success or fail from getUserMedia() 
	*/

	recordButton.disabled = true;
	stopButton.disabled = false;
	pauseButton.disabled = false

	/*
    	We're using the standard promise based getUserMedia() 
    	https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia
	*/

	navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
		console.log("getUserMedia() success, stream created, initializing Recorder.js ...");

		/*
			create an audio context after getUserMedia is called
			sampleRate might change after getUserMedia is called, like it does on macOS when recording through AirPods
			the sampleRate defaults to the one set in your OS for your playback device

		*/
		audioContext = new AudioContext();

		//update the format 
		document.getElementById("formats").innerHTML="Format: 1 channel pcm @ "+audioContext.sampleRate/1000+"kHz"

		/*  assign to gumStream for later use  */
		gumStream = stream;
		
		/* use the stream */
		input = audioContext.createMediaStreamSource(stream);

		/* 
			Create the Recorder object and configure to record mono sound (1 channel)
			Recording 2 channels  will double the file size
		*/
		rec = new Recorder(input,{numChannels:1})

		//start the recording process
		rec.record()

		console.log("Recording started");

	}).catch(function(err) {
	  	//enable the record button if getUserMedia() fails
    	recordButton.disabled = false;
    	stopButton.disabled = true;
    	pauseButton.disabled = true
	});
}

function pauseRecording(){
	console.log("pauseButton clicked rec.recording=",rec.recording );
	if (rec.recording){
		//pause
		rec.stop();
		pauseButton.innerHTML="مواصلة";
	}else{
		//resume
		rec.record()
		pauseButton.innerHTML="إيقاف";

	}
}

function stopRecording() {
	console.log("stopButton clicked");

	//disable the stop button, enable the record too allow for new recordings
	stopButton.disabled = true;
	recordButton.disabled = false;
	pauseButton.disabled = true;

	//reset button just in case the recording is stopped while paused
	pauseButton.innerHTML="إيقاف";
	
	//tell the recorder to stop the recording
	rec.stop();

	//stop microphone access
	gumStream.getAudioTracks()[0].stop();

	//create the wav blob and pass it on to createDownloadLink
	rec.exportWAV(createDownloadLink);
}

function createDownloadLink(blob) {
	
	var url = URL.createObjectURL(blob);
	var au = document.createElement('audio');
	var li = document.createElement('li');
	

	//name of .wav file to use during upload and download (without extendion)
	var filename = Date.now();

	//add controls to the <audio> element
	au.controls = true;
	au.src = url;

	//save to disk link
	var link = document.createElement('a');
	link.href = url;
	link.download = filename+".wav"; //download forces the browser to donwload the file using the  filename
	link.innerHTML = "حفظ الملف على الحاسوب";

	//add the new audio element to li
	li.appendChild(au);
	
	//add the filename to the li
	li.appendChild(document.createTextNode(filename+".wav "))

	//add the save to disk link to li
	//li.appendChild(link);
	
	//upload link
	var upload = document.createElement('a');
	upload.href="#";
	upload.innerHTML = "رفع الملف الصوتي";
	upload.addEventListener("click", function(event){
		  var xhr=new XMLHttpRequest();
		  xhr.onload=function(e) {
		      if(this.readyState === 4) {
		          console.log("Server returned: ",e.target.responseText);
		      }
		  };
		  var fd=new FormData();
		  fd.append("audio_data",blob, filename);
		  xhr.open("POST","/recorder/uploader.php",true);
		  xhr.send(fd);
		  audio_filename = filename;
		  $("#answer").html('<p class="success-message">تمت مباشرة حفظ الملف يرجى مواصلة حفظ البيانات</p>');
	})
	li.appendChild(document.createTextNode (" "))//add a space in between
	li.appendChild(upload)//add the upload link to li

	//add the li element to the ol
	recordingsList.appendChild(li);
}
