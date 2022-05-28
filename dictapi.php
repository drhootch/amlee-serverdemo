<?php

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 * Private APIs for the frontend
 * 
 *  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

// Allow API headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");


mb_internal_encoding("UTF-8");

//~ ini_set('display_errors', 1);
//~ ini_set('display_startup_errors', 1);
//~ error_reporting(E_ALL & ~E_NOTICE);



if(isset($_GET['query']) && $_GET['query'] == "FAD"){

	echo getDictEntry($_GET['POS']??"", $_GET['pattern']??"");
}


if(isset($_GET['word']) && isset($_GET['query'])){
	
	if($_GET['query'] == "POS"){
		echo getAraToolsPOS($_GET['word']);
	}else if($_GET['query'] == "lemma"){
		echo getFarasaLemma($_GET['word']);
	}
}
		
		

/* -----------------------------------------------------------------------------
	A function to access the data of FreeArabicDictionary.com owned by Fabienne Hadek
	* To which we have permission to use
	----------------------------------------------------------------------------- */

function getAmlyRecord($type="", $pattern = "") {

	require 'config.php';


	$connection = new PDO( 'mysql:host='.$db_config['host'].';dbname='.$db_config['dbname'].';port='.$db_config['port'].';charset=utf8;', $db_config['username'], $db_config['password'] );

	$statement = $connection->prepare("SELECT * FROM records WHERE status like 'Enabled'".
		(!empty($type)   ? ' AND `type` like :type' : null).
		(!empty($pattern)   ? ' AND `text` regexp :pattern' : null).
		" ORDER BY RAND() LIMIT 1");

	if (!empty($type)) {
		$statement->bindParam(':type', $POS);
	}
	if (!empty($pattern)) {
		$statement->bindParam(':pattern', $pattern);
	}

	

	$statement->execute();
	$result = $statement->fetchAll();
	
	//$filtered_rows = $statement->rowCount();

	$data = array();
	
	foreach($result as $row)
	{

		$data = array(
			"audio" => 'https://amly.app/audio/fad/'.$row["Media"].'.mp3', // maybe null
			"text" => $row["text"],
			"img" => $row["image"],
		);
	}
	
	return $data;

}


/* -----------------------------------------------------------------------------
	A function to access the data of FreeArabicDictionary.com owned by Fabienne Hadek
	* To which we have permission to use
	----------------------------------------------------------------------------- */

function getDictEntry($POS, $pattern){

	require 'config.php';


	$connection = new PDO( 'mysql:host='.$db_config['host'].';dbname='.$db_config['dbname'].';port='.$db_config['port'].';charset=utf8;', $db_config['username'], $db_config['password'] );

	$statement = $connection->prepare("SELECT * FROM FreeDictionary WHERE 1".
		(!empty($POS)   ? ' AND `Type` like :POS' : null).
		(!empty($pattern)   ? ' AND `Arabic` regexp :pattern' : null).
		" ORDER BY RAND() LIMIT 1");

	if (!empty($POS)) {
		$statement->bindParam(':POS', $POS);
	}
	if (!empty($pattern)) {
		$statement->bindParam(':pattern', $pattern);
	}


	$statement->execute();
	$result = $statement->fetchAll();
	
	//$filtered_rows = $statement->rowCount();

	$data = array();
	

	foreach($result as $row)
	{

		$data = array(
			"audio" => 'https://amly.nbyl.me/audio/fad/'.$row["Media"].'.mp3', // maybe null
			"text" => $row["text"],
			"en" => $row["English"],
			"ar" => $row["Arabic"],
			"pl" => $row["Plural"],
			"verb" => $row["Verbform"],
		);
		
		if($data["pl"] != "" && $data["verb"] == ""){
			$data["text"] = $data["ar"]." ".$data["pl"];

		}else{
			$data["text"] = $data["ar"];
			$data["text"] = str_replace(" ، " ," ",$data["text"]);
			$data["text"] = str_replace("،" ," ",$data["text"]);
		}
	}
	
	return json_encode($data);

}


/* -----------------------------------------------------------------------------
	A function to access the API of Aratools.com managed by André Lynum
	The project is being moved to a fully open source model
	----------------------------------------------------------------------------- */

function getAraToolsPOS($word){

	//verify that words is arabic 
	
	$url = 'http://aratools.com/dict-service?query={%22dictionary%22:%22'.'AR-EN-WORD-DICTIONARY'.'%22,%22word%22:%22'.urlencode($_GET['word']).'%22,%22dfilter%22:true}';
	$data = file_get_contents($url);
		
	$dom = new DOMDocument();
	$dom->loadHTML($data);
	$table = $dom->getElementsByTagName('td');


	$return = [
		"POS"=> $table->item(7)->textContent,
		"root"=> $table->item(8)->textContent
	];

	
	return json_encode($return);
}

/* -----------------------------------------------------------------------------
	Get lemma of a word or a text using Farasa API
	* Because this API is slow, we made a cache for it as our words change rarely
	----------------------------------------------------------------------------- */

function getFarasaLemma($text){
	

	$connection = new PDO( 'mysql:host='.$db_config['host'].';dbname='.$db_config['dbname'].';port='.$db_config['port'].';charset=utf8;', $db_config['username'], $db_config['password'] );

	$statement = $connection->prepare("SELECT * FROM lemmatizer_cache WHERE text like :text");
	$statement->bindParam(':text', $text);
	
	$statement->execute();
	$result = $statement->fetchAll();
	$data = array();
	$exists = $statement->rowCount();
	
	if($exists > 0) { // Cached in our DB

		return json_encode($result[0]["lemmatized"]);
		
	}else { // Not cached fetch API

		// FARASA API KEY
		$params = array(
			'api_key' => 'hoxIHPHtKQJlnwPvtr'
		);
		
		$api_answer = file_get_contents( "https://farasa.qcri.org/webapi/lemmatization/?text=".urlencode($text)."&api_key=".$params['api_key']);

		if($api_answer){
			$lemmatized = (json_decode($api_return));
			$statement = $connection->prepare("INSERT INTO lemmatizer_cache (text, lemmatized) VALUES (:text, :lemmatized)")->execute(
				array(
					"text"=>$text,
					"lemmatized"=>$api_answer,
				)
			);
		}else
			$api_answer = "";


		return json_encode($api_answer);
	}
}


	





