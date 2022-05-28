<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

require '../config.php';

$connection = new PDO( 'mysql:host='.$db_config['host'].';dbname='.$db_config['dbname'].';port='.$db_config['port'].';charset=utf8;', $db_config['username'], $db_config['password'] );


if(isset($_POST['dataform'])){
	
	if($_POST['dataform'] = "c3EQ1mU7j6Lb"){
		
		//check if the word was not added before
		
		try {
			$connection = new PDO( 'mysql:host=localhost;dbname=arhub_amly;charset=utf8;', $username, $password );
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $connection->prepare("INSERT INTO records(type,text,level,classification,fingerprint,audio) VALUES(:type,:text,:level,:classification,:fingerprint,:audio)");

			$audio = $_POST['audio'].'.wav';

			//Now you have first retrieve data, you can bind
			//$stmt->bindParam(':status', 'enabled'); 
			$stmt->bindParam(':type', $_POST['type']); 
			$stmt->bindParam(':text', $_POST['text']);
			$stmt->bindParam(':level', $_POST['level']); 
			$stmt->bindParam(':classification', $_POST['classification']); 
			$stmt->bindParam(':audio', $audio); 
			$stmt->bindParam(':fingerprint', hashsimplifyText($_POST['text'], $_POST['type']) );  //it is unique
			$stmt->execute();
			
			echo "تمت الإضافة بنجاح.";
		}catch(PDOException $e){
			echo "Error:" . $e->getMessage();
		}
		
		$connection = null;
	}
		
}

if(isset($_FILES['imagefile'])){
	
	
   if(isset($_FILES['imagefile'])){
	  $errors= array();
	  $file_name = $_FILES['imagefile']['name'];
	  $file_size =$_FILES['imagefile']['size'];
	  $file_tmp =$_FILES['imagefile']['tmp_name'];
	  $file_type=$_FILES['imagefile']['type'];
	  $file_ext=strtolower(end(explode('.',$_FILES['imagefile']['name'])));
	  
	  $extensions= array("jpeg","jpg","png","gif");
	  
	  if(in_array($file_ext,$extensions)=== false){
		 $errors[]="امتداد الملف غير مقبول، يرجى اختيار ملفات من صيغ: JPEG، GIF أو PNG فقط.";
	  }
	  
	  if($file_size > 2097152){
		 $errors[]='يجب أن يتعدى حجم الصورة 2 ميغابايت';
	  }
	  
	  if(empty($errors)==true){
		 move_uploaded_file($file_tmp,"/home/arhub/amly/".$file_name);
		 echo '<p class="success-message">تم رفع الصورة بنجاح</p>';
	  }else{
		 echo '<p class="error-message">';
		 print_r($errors);
		 echo "</p>";
	  }
   }
}

if(isset($_FILES['audio_data'])){


	print_r($_FILES); //this will print out the received name, temp name, type, size, etc.


	$size = $_FILES['audio_data']['size']; //the size in bytes
	$input = $_FILES['audio_data']['tmp_name']; //temporary name that PHP gave to the uploaded file
	$output = $_FILES['audio_data']['name'].".wav"; //letting the client control the filename is a rather bad idea

	//move the file from temp name to local folder using $output name
	move_uploaded_file($input, "/home/arhub/amly/audio/records/".$output);

}



/* ############################################################################ */

/**
 * Clean a word from declensions/harakaat, we don't need to remove ال we'll just make a remark about it I aguess
 * @param  string  $word
 * @return string
 * mb_internal_encoding("UTF-8");
 */

function hashsimplifyText($text, $type){ 
	
	//TODO: remove punctuation or non arabic chars & keep hamzat; but how if a word has different harakat? or clean only tanween & tatweel for words and all for the rest
	
	if($type == "word")
		$toremove = array("ْ", "ً", "ُ", "ٍ", "ـ"); // remove only sukoon, tanweens & tatweel
	else
		$toremove = array("َ", "ً", "ُ", "ٌ", "ِ", "ٍ", "ّ", "ْ", "ـ", "،", ".", ":", "؟", "؛", "!"); // remove all declensions, tatweel, punctuation
	
	//clean <br> and space redondancy & \n \t or keep only the arabic letter from the beginning

	return md5(str_replace($toremove, "", $text));
}
