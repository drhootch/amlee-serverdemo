<?php
/* ############################################################################ */

// Allow API headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");

mb_internal_encoding("UTF-8");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);



/* ############################################################################ */

require_once 'GameData.php';


/* ############################################################################ */


// A demonstration of possible games

if(isset($_GET['type'])){

	$type = $_GET['type'];

	$game = new GameData();



	// Foster a specific game for demo
	if(rand(0,1))
		$_GET['gameid'] = 0;


	if($type == "word"){ // will be dictentry dictionary word

		if(isset($_GET['	']))
			$randomGame = intval($_GET['gameid']);
		else
			$randomGame = rand(0,7);

		/* ***************************************************** */
		if($randomGame == 0){ // Textarea of dictionnary words

			$entry = $game->getFreeDictionaryData();

			$data = array(
				"type" => "text_write",
				"q" => "استمع واكتب",
				"q2" => "",
				"audio" => $entry['audio'], // maybe null
				"answer" => $entry['text'],
				//"hasImg" => false,
				"img" => null,
				"imgTag" => "",
				"hint" => "",
				"attempts" =>  0,
			);
			
			// Tag For image API
			/* 
			if(rand(0,1)){
				$data["imgTag"] = $entry["English"];
				$data["img"] = null;
			}else{
				$data["imgTag"] = "";
				$data["img"] = null;
			}*/

			echo json_encode($data);
		}
		/* ***************************************************** */
		else if($randomGame == 1){ // time case

			$time = $game->getTimeGame();

			$table = array(
				'type' => 'text_write',
				'q' => 'كم الساعة؟ (مثل الساعة الرابعة وخمس دقائق)',
				'q2' => $time["time"],
				//example tag?
				"imgTag"=> null,
				"audio" => null, // may be null
				"answer" => $time["string"],
				"img" => 'https://res.cloudinary.com/demo/image/upload/$minute_'.$time["minutes"].'/$hour_'.$time["hour"].'/$ma_$minute_div_60_mul_360/$ha_$hour_div_12_mul_360/l_clock_example:small/a_$ha_add_$ma_div_12/fl_layer_apply/l_clock_example:big/a_$ma/fl_layer_apply/w_550,h_550,c_crop/q_auto/clock_example/clock.png',
				"attempts" =>  0,
				"gameclass" =>  "time",
			);
			echo json_encode($table);
		}
		/* ***************************************************** */
		else if($randomGame == 2){ // number case
			$number = $game->getNumberGame();

			$table = array(
				'type' => 'text_write',
				'q' => 'اكتب هذا العدد بالحروف',
				'q2' => $number["value"],
				"imgTag"=> null,
				"audio" => null, // may be null
				"answer" => $number["string"],
				"img" => '',
				"attempts" =>  0,
			);
			echo json_encode($table);
		}
		/* ***************************************************** */
		else if($randomGame == 3){
			
			
			$rows = [
				["كَمْ عَدَدُ أَيّامِ الأُسْبوعِ؟", "سَبْعَة", null, ""],
				["ما عاصِمَة مِصْر؟", "الرَِيّاض", null, "https://cdn.pixabay.com/photo/2019/05/06/05/15/pyramid-4182255_960_720.png"],
				["ما عاصِمَة المملكة العربية السعودية؟", "الرَِيّاض", null, "https://cdn.pixabay.com/photo/2016/01/20/09/10/saudi-arabia-1151148_960_720.jpg"],
				["ما عاصِمَة الأردن؟", "عَمّان", null, ""],
				["ما الفِعْلُ الماضي مِنْ (يدعو)؟", "دَعا", null, ""],
				["ما الفِعْلُ الماضي مِنْ (يسعى)", "سَعى", null, ""],
				["ما الفِعْلُ الماضي مِنْ (يجري)؟", "جَرى", null, ""],
			];
			
			
			$randomRows = rand(0, count($rows)-1);
			
			$table = array(
				'type' => 'text_write',
				'q' => 'أجب عن السؤال التالي:',
				'q2' => $rows[$randomRows][0],
				"imgTag"=> $rows[$randomRows][2],
				"audio" => null, // may be null
				"answer" => $rows[$randomRows][1],
				"img" => null,
				"attempts" =>  rand(0,4),
			);

			echo json_encode($table);

		}
		/* ***************************************************** */
		else if($randomGame == 4){

			$rows = [
				["اختر الكتابة الصحيحة", "خَطَأ", array('خَطَأ', 'خَطَء', 'خَطَئ', 'خَطَؤ'), null, ""],
				["اختر الكتابة الصحيحة", "سَيّارَة", array('سَيارَت', 'سَيَارَه', 'سَيَّرَة', 'سيارَةٌ'), null, ""],
				["اختر الكتابة الصحيحة", "سَيّارَة", array('سَيارَت', 'سَيَارَه', 'سَيَّرَة', 'سيارَةٌ'), null, ""],
				["اختر الكتابة الصحيحة", "ذِئْب", array('ذءب', 'ذأب', 'ذئب', 'ذؤب'), null, ""],
				["اختر الكتابة الصحيحة", "رَأَيْتُكِ", array('رأيتك', 'رأيتكي'), null, ""],
			];
			
			$randomRows = rand(0, count($rows)-1);
			
			$table = array(
				'type' => 'single_choice',
				'q' => $rows[$randomRows][0],
				"imgTag"=> $rows[$randomRows][3],
				"choices" => $rows[$randomRows][2],
				"audio" => null, // maybe null
				"answer" => $rows[$randomRows][1],
				"img" => $rows[$randomRows][4],
				"attempts" =>  rand(0,2),
			);

			echo json_encode($table);

		}
		/* ***************************************************** */
		else if($randomGame == 5){ // Example of a voice effect

			if(rand(0,1))
				$table = array(
					'type' => 'text_write',
					'q' => 'استمع واكتب',
					'q2' => '',
					"audio" => "https://amly.app/audio/records/1653410952269.wav", // maybe null
					"answer" => "سَأَلَتِ البِنْتُ والِدَها عَنْ رَأْيِهِ في أَمْرِ الرِّحْلَةِ.",
					"img" => "",
					"imgTag" => null,
					"attempts" =>  0,
				);
			else
				$table = array(
					'type' => 'text_write',
					'q' => 'استمع واكتب',
					'q2' => '',
					"audio" => "https://amly.app/audio/records/1653410340459.wav", // maybe null
					"answer" => "تَتَكَوَّنُ المَقالَةُ المَوْضوعِيَّةُ مِنْ مُقَدّمَةٍ وَعَرْضٍ وَخاتِمَةٍ.",
					"img" => "",
					"imgTag" => null,
					"attempts" =>  0,
				);
			

			echo json_encode($table);

		}
		/* ***************************************************** */
		else if($randomGame == 8){ // Example of records

			$row = $game->getRecord("sentence");
			
			$table = array(
				'type' => 'text_write',
				'q' => 'استمع واكتب',
				'q2' => '',
				"audio" => $row["audio"],
				"answer" => $row["text"],
				"img" => "",
				"imgTag" => null,
				"attempts" =>  0,
			);

			echo json_encode($table);

		}
		/* ***************************************************** */
		else if($randomGame == 6){


			$rows = [
				['ركّب حروف الكلمة التالية', "بُرْتُقال", "https://cdn.pixabay.com/photo/2017/01/20/15/12/oranges-1995079_960_720.jpg", "orange.mp3"],
				['ركّب حروف الكلمة التالية', "بِنْت", null, "girl.wav"],
				['ركّب حروف الكلمة التالية', "خُبْز", "https://cdn.pixabay.com/photo/2020/11/01/02/29/bread-5702703__480.jpg", "bread.wav"],
				['ركّب حروف الكلمة التالية', "نَمِر",  "https://cdn.pixabay.com/photo/2014/11/03/17/40/leopard-515509_960_720.jpg", "tiger.wav"],
				['ركّب حروف الكلمة التالية', "أَسد", "https://cdn.pixabay.com/photo/2015/06/02/12/11/lion-794962_1280.jpg", "lion.wav"],
				['ركّب حروف الكلمة التالية', "قِطَّة", "https://cdn.pixabay.com/photo/2017/02/15/12/12/cat-2068462_960_720.jpg", "cat.wav"],
			];

			$randomRows = rand(0, count($rows)-1);

			$table = array(
				'type' => 'word_building',
				'q' => 'ركّب حروف الكلمة التالية',
				'q2' => '',
				"img"=> $rows[$randomRows][2],
				"audio" => "https://amly.app/audio/records/".$rows[$randomRows][3],
				"answer" => $rows[$randomRows][1],
				"imgTag" => null,
				"attempts" =>  3,
			);

			echo json_encode($table);

		}
		/* ***************************************************** */
		else if($randomGame == 7){

			$table = array(
				'type' => 'word_building',
				'q' => 'أَكْمِل الفَراغ التالي:',
				'q2' => 'ذ . ب',
				"img"=> "https://cdn.pixabay.com/photo/2012/10/25/23/52/wolf-62898_960_720.jpg",
				"audio" => "https://amly.app/audio/records/wolf.mp3", // maybe null
				"answer" => "ذئِب",
				"imgTag" => null,
				"attempts" =>  3,
			);

			echo json_encode($table);

		}
	}
}

/* ############################################################################ */


else if(isset($_GET['generateGame']) && isset($_GET['level']) && isset($_GET['errorFrequency']) && isset($_GET['rule'])){

	$level = floatval($_GET['level']);
	$errorFrequency = floatval($_GET['error']);
	$rule = floatval($_GET['rule']);

	// @todo add country
	$game = new GameData();
	echo $game->suggestAGame($level, $errorFrequency, $rule);

}


else{
	echo "Invalid API";
}
