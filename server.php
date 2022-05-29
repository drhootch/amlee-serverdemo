<?php

/* ####################################################
 *	An implementation of a basic API
 *
 * #################################################### */

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

// Example of internalization
$i18n = [
	"Listen&Write" => ["ar"=>"استمع واكتب", "en"=>"Listen and write"],
	"?" => ["ar"=>"؟", "en"=>"?"],
	"howmuch" => ["ar"=>"كم", "en"=>"How much is"],
	"inAr" => ["ar"=>"", "en"=>"in Arabic"],
	"WhatTime" => ["ar"=>"كم الساعة؟", "en"=>"What time is it? (in Arabic)"],
	"answerQuestion" => ["ar"=> "أجب عن السؤال التالي:", "en"=>"Answer the following question:"],
	"writeNumber" => ["ar"=> "اكتب هذا العدد بالحروف.", "en"=>"Write the following number in Arabic words."],
	"selectValidWriting" => ["ar"=>  "اختر الكتابة الصحيحة.", "en"=>"Choose the right spelling."],
	"buildWord" => ["ar"=>  "ركّب حروف الكلمة التالية.", "en"=>"Rearrange the following letters."],
	"fillGap" => ["ar"=> "أَكْمِل الفَراغ التالي:", "en"=>"Complete the missing letter"], // Fill the gap or complete the missing letter
];


// The game can theoretically generate an infinite possible games
// A demonstration of few possible games:

if(isset($_GET['getGame'])){

	$game = new GameData();

	// IA Suggestion
	if(isset($_GET['level']) && isset($_GET['gravity']) && isset($_GET['rule'])) { 	// get level & errors from cookie or as a $_GET from the frontend engine otherwise randomize until level is defined

		// To browse his errors
		$track = json_decode($_COOKIE["track"]);

		$level = floatval($_GET['level']);
		$errorFrequency = floatval($_GET['gravity']);
		$rule = floatval($_GET['rule']);

		//isset($_COOKIE["track"])
		//$level = floatval($_GET['level']);
		//$errorFrequency = floatval($track->rules[5]["scale"]);
		//$rules have to be linked with a fixed number, in order to be processed
		//$rule = $track->rules[5]["category"].$track->rules[5]["id"];

		echo $game->suggestAGame($level, $errorFrequency, $rule); // Call IA to determine the adequate game
	}

	// Default Lang
	$lang = "ar";
	if(isset($_GET['lang']) && in_array($_GET['lang'], array('ar', 'en')))
		$lang = $_GET['lang'];

	// Foster a specific game for demo
	if(!isset($_GET['gameid']) && !rand(0,2))
		$_GET['gameid'] = 0;

	if(isset($_GET['gameid']))
		$gameType = intval($_GET['gameid']);
	else
		$gameType = rand(0, 9);

	/* *****************************************************
		Some cases:
		* Verbs +level 3
	 ***************************************************** */
	if($gameType == 0){ // Dictionnary words

		$entry = $game->getFreeDictionaryData();

		$data = array(
			"type" => "text_write",
			"q" => $i18n["Listen&Write"][$lang],
			"q2" => "",
			"audio" => $entry['audio'], // maybe ""
			"answer" => $entry['text'],
			//"hasImg" => false,
			"img" => "",
			"imgTag" => "",
			"hint" => "",
			"attempts" =>  0
		);

		// Tag For image API
		/*
		if(rand(0,1)){
			$data["imgTag"] = $entry["English"];
			$data["img"] = "";
		}else{
			$data["imgTag"] = "";
			$data["img"] = "";
		}*/

	}
	/* ***************************************************** */
	else if($gameType == 1){ // time case

		$time = $game->getTimeGame();

		$data = array(
			'type' => 'text_write',
			'q' => $i18n["WhatTime"][$lang]. ' (مثل الساعة الرابعة وخمس دقائق)',
			'q2' => $time["time"],
			//example tag?
			"imgTag"=> "",
			"audio" => "", // may be ""
			"answer" => $time["string"],
			"img" => 'https://res.cloudinary.com/demo/image/upload/$minute_'.$time["minutes"].'/$hour_'.$time["hour"].'/$ma_$minute_div_60_mul_360/$ha_$hour_div_12_mul_360/l_clock_example:small/a_$ha_add_$ma_div_12/fl_layer_apply/l_clock_example:big/a_$ma/fl_layer_apply/w_550,h_550,c_crop/q_auto/clock_example/clock.png',
			"attempts" =>  0,
			"gameclass" =>  "time",
		);
	}
	/* ***************************************************** */
	else if($gameType == 2){ // number case
		$number = $game->getNumberGame();

		$data = array(
			'type' => 'text_write',
			'q' => $i18n["writeNumber"][$lang],
			'q2' => $number["value"],
			"imgTag"=> "",
			"audio" => "", // may be ""
			"answer" => $number["string"],
			"img" => '',
			"attempts" =>  0,
		);
	}
	/* ***************************************************** */
	else if($gameType == 9){ // Maths

		$a = rand(1,20);
		$b = rand(1,20);

		$number = $game->getNumberGame(null, $a, $b);

		$data = array(
			'type' => 'text_write',
			'q' => $i18n["howmuch"][$lang].' '.$a.'+'.$b.' '.$i18n["inAr"][$lang].$i18n["?"][$lang],
			'q2' => $number["value"],
			"imgTag"=> "",
			"audio" => "", // may be ""
			"answer" => $number["string"],
			"img" => '',
			"attempts" =>  0,
		);
	}
	/* ***************************************************** */
	else if($gameType == 3){ // General & grammatical questions (grammar is good for advanced levels)

		$rows = [
			["كَمْ عَدَدُ أَيّامِ الأُسْبوعِ؟", "سَبْعَة", "", ""],
			["ما عاصِمَة مِصْر؟", "القاهِرَة", "", "https://cdn.pixabay.com/photo/2019/05/06/05/15/pyramid-4182255_960_720.png"],
			["ما عاصِمَة السعودية؟", "الرَِيّاض", "", "https://cdn.pixabay.com/photo/2016/01/20/09/10/saudi-arabia-1151148_960_720.jpg"],
			["ما عاصِمَة الأردن؟", "عَمّان", "", ""],
			["ما الماضي مِنْ (يدعو)؟", "دَعا", "", ""],
			["ما الماضي مِنْ (يسعى)", "سَعى", "", ""],
			["ما الماضي مِنْ (يجري)؟", "جَرى", "", ""],
		];


		$randomRows = rand(0, count($rows)-1);

		$data = array(
			'type' => 'text_write',
			'q' => $i18n["answerQuestion"][$lang],
			'q2' => $rows[$randomRows][0],
			"imgTag"=> $rows[$randomRows][2],
			"audio" => "", // may be ""
			"answer" => $rows[$randomRows][1],
			"img" => "",
			"attempts" =>  rand(0,4),
		);

	}
	/* ***************************************************** */
	else if($gameType == 4){

		$rows = [
			["اختر الكتابة الصحيحة", "خَطَأ", array('خَطَأ', 'خَطَء', 'خَطَئ', 'خَطَؤ'), "", ""],
			["اختر الكتابة الصحيحة", "سَيّارَة", array('سَيارَت', 'سَيَارَه', 'سَيَّرَة', 'سيارَةٌ'), "", ""],
			["اختر الكتابة الصحيحة", "سَيّارَة", array('سَيارَت', 'سَيَارَه', 'سَيَّرَة', 'سيارَةٌ'), "", ""],
			["اختر الكتابة الصحيحة", "ذِئْب", array('ذءب', 'ذأب', 'ذئب', 'ذؤب'), "", ""],
			["اختر الكتابة الصحيحة", "رَأَيْتُكِ", array('رأيتك', 'رأيتكي'), "", ""],
		];

		$randomRows = rand(0, count($rows)-1);

		$data = array(
			'type' => 'single_choice',
			'q' => $i18n["selectValidWriting"][$lang],
			"imgTag"=> $rows[$randomRows][3],
			"choices" => $rows[$randomRows][2],
			"audio" => "", // maybe ""
			"answer" => $rows[$randomRows][1],
			"img" => $rows[$randomRows][4],
			"attempts" =>  rand(0,2),
		);

	}
	/* ***************************************************** */
	else if($gameType == 5){ // Example of a voice effect

		if(rand(0,1))
			$data = array(
				'type' => 'text_write',
				'q' => $i18n["Listen&Write"][$lang],
				'q2' => '',
				"audio" => "https://amly.app/audio/records/1653410952269.wav", // maybe ""
				"answer" => "سَأَلَتِ البِنْتُ والِدَها عَنْ رَأْيِهِ في أَمْرِ الرِّحْلَةِ.",
				"img" => "",
				"imgTag" => "",
				"attempts" =>  0,
			);
		else
			$data = array(
				'type' => 'text_write',
				'q' => $i18n["Listen&Write"][$lang],
				'q2' => '',
				"audio" => "https://amly.app/audio/records/1653410340459.wav", // maybe ""
				"answer" => "تَتَكَوَّنُ المَقالَةُ المَوْضوعِيَّةُ مِنْ مُقَدّمَةٍ وَعَرْضٍ وَخاتِمَةٍ.",
				"img" => "",
				"imgTag" => "",
				"attempts" =>  0,
			);
	}

	/* ***************************************************** */
	else if($gameType == 6){ // diactricts can also be object of order


		$rows = [
			['ركّب حروف الكلمة التالية',"بُرْتُقال" , "برتقال", "https://cdn.pixabay.com/photo/2017/01/20/15/12/oranges-1995079_960_720.jpg", "orange.mp3"],
			//['ركّب حروف الكلمة التالية', "بِنْت", "", "girl.wav"],
			['ركّب حروف الكلمة التالية',"خُبْز" , "خبز", "https://cdn.pixabay.com/photo/2020/11/01/02/29/bread-5702703__480.jpg", "bread.mp3"],
			//['ركّب حروف الكلمة التالية', "نمر",  "https://cdn.pixabay.com/photo/2014/11/03/17/40/leopard-515509_960_720.jpg", "tiger.mp3"],
			['ركّب حروف الكلمة التالية',"أَسَد" , "أسد", "https://cdn.pixabay.com/photo/2015/06/02/12/11/lion-794962_1280.jpg", "lion.mp3"],
			['ركّب حروف الكلمة التالية',"قِطَّة" , "قِطَّة", "https://cdn.pixabay.com/photo/2017/02/15/12/12/cat-2068462_960_720.jpg", "cat.mp3"],
		];

		$randomRows = rand(0, count($rows)-1);

		$data = array(
			'type' => 'word_building',
			'q' => $i18n["buildWord"][$lang],
			'q2' => '',
			"img"=> $rows[$randomRows][3],
			"audio" => "https://amly.app/audio/records/".$rows[$randomRows][4],
			"answer" => $rows[$randomRows][2],
			"imgTag" => "",
			"attempts" =>  3,
			//"tts" => $rows[$randomRows][1] // and audio ""
		);

	}
	/* ***************************************************** */
	else if($gameType == 6){ // Example of voicing TTS


		$rows = [
			['ركّب حروف الكلمة التالية',"بُرْتُقال" , "برتقال", "https://cdn.pixabay.com/photo/2017/01/20/15/12/oranges-1995079_960_720.jpg", "orange.mp3"],
			//['ركّب حروف الكلمة التالية', "بِنْت", "", "girl.wav"],
			['ركّب حروف الكلمة التالية',"خُبْز" , "خبز", "https://cdn.pixabay.com/photo/2020/11/01/02/29/bread-5702703__480.jpg", "bread.mp3"],
			//['ركّب حروف الكلمة التالية', "نمر",  "https://cdn.pixabay.com/photo/2014/11/03/17/40/leopard-515509_960_720.jpg", "tiger.mp3"],
			['ركّب حروف الكلمة التالية',"أَسَد" , "أسد", "https://cdn.pixabay.com/photo/2015/06/02/12/11/lion-794962_1280.jpg", "lion.mp3"],
			['ركّب حروف الكلمة التالية',"قِطَّة" , "قِطَّة", "https://cdn.pixabay.com/photo/2017/02/15/12/12/cat-2068462_960_720.jpg", "cat.mp3"],
		];

		$randomRows = rand(0, count($rows)-1);

		$data = array(
			'type' => 'word_building',
			'q' => $i18n["buildWord"][$lang],
			'q2' => '',
			"img"=> $rows[$randomRows][3],
			"audio" => "https://amly.app/audio/records/".$rows[$randomRows][4],
			"answer" => $rows[$randomRows][2],
			"imgTag" => "",
			"attempts" =>  3,
			//"tts" => $rows[$randomRows][1] // and audio ""
		);

	}
	/* ***************************************************** */
	else if($gameType == 7){


		$rows = [
			['ذِ . ب', 'ئ', array("أ", "ئ", "ء", "ى"), "wolf.mp3", "https://cdn.pixabay.com/photo/2012/10/25/23/52/wolf-62898_960_720.jpg"],
			["بِنـ.", "ت", array("ه", "ة", "ت", "ن"), "girl.wav", ""],
		];

		$randomRows = rand(0, count($rows)-1);

		$data = array(
			'type' => 'single_choice',
			'q' => $i18n["fillGap"][$lang],
			'q2' => $rows[$randomRows][0],
			"img"=> $rows[$randomRows][4],
			"audio" => "https://amly.app/audio/records/".$rows[$randomRows][3],
			"answer" => $rows[$randomRows][1],
			"choices" => $rows[$randomRows][2],
			"imgTag" => "",
			"attempts" =>  3,
		);

	}
	/* ***************************************************** */
	else if($gameType == 8){ // Example of sentence records

		$row = $game->getRecord("sentence");
		$data = array(
			'type' => 'text_write',
			'q' => $i18n["Listen&Write"][$lang],
			'q2' => '',
			"audio" => $row["audio"],
			"answer" => $row["text"],
			"img" => "",
			"imgTag" => "",
			"attempts" =>  0,
		);
	}

	$data["gameid"] = $gameType;
	echo json_encode($data);

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
