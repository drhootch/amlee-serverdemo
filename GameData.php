<?php

require 'config.php';
require 'dictapi.php';

require_once __DIR__ . '/vendor/autoload.php';
use Phpml\Classification\KNearestNeighbors;
use Phpml\ModelManager;
use Phpml\Dataset\ArrayDataset;
use Phpml\Dataset\CsvDataset;

class GameData {

    
   /* -----------------------------------------------------------------------------
	*	A function to access the data of FreeArabicDictionary.com owned by Fabienne Hadek
	*	To which we have permission to use
	----------------------------------------------------------------------------- */
	
    function getFreeDictionaryData($POS = "", $pattern = "") {
		
		///getDictEntry($POS, $pattern);

		$url = 'https://amly.app/dictapi.php?query=FAD&POS='.$POS.'&pattern='.$pattern;
		$data = (array) json_decode(file_get_contents($url));
		return $data;
    }


   /* -----------------------------------------------------------------------------
		Returns recorded samples from the DB
		* type: enum('syllable', 'word', 'sentence', 'paragraph') 
		* status  enum('Disabled', 'Enabled', 'To Revise', '') 
	----------------------------------------------------------------------------- */
    function getRecord($type="", $pattern = "") {
	
		return getAmlyRecord($type, $pattern);
    }
    

	/* -----------------------------------------------------------------------------
		Returns time's wording
	----------------------------------------------------------------------------- */
    function getTimeGame($hour = null, $minutes = null) {
		
		if(is_null($hour))
			$hour = rand(2, 12);
		if(is_null($minutes))
			$minutes = rand(10, 60);
		
		require 'vendor/autoload.php';	
		$Arabic = new \ArPHP\I18N\Arabic();
		$Arabic->setNumberFeminine(2);
		$Arabic->setNumberFormat(1);
		$Arabic->setNumberOrder(2);
				
		
		$h_text = $Arabic->int2str($hour);
		
		$Arabic->setNumberOrder(1);
		$m_text = $Arabic->arPlural('دقيقة', $minutes, 'دقيقتين', 'دقائق', 'دَقيقَةً');
		$m_text = str_replace('%d', $Arabic->int2str($minutes), $m_text);
			

		return [
			"hour"=> $hour,
			"minutes"=> $minutes,
			"time"=> $hour.':'.$minutes,
			"string"=>$h_text." و".$m_text
		];        
    }
    

	/* -----------------------------------------------------------------------------
		Returns numbers' wording
	----------------------------------------------------------------------------- */
    function getNumberGame($value = null) {

		require 'vendor/autoload.php';

		if(is_null($value))
			$value = rand(1, 1000);

		$Arabic = new \ArPHP\I18N\Arabic();

		$Arabic->setNumberFeminine(1);
		$Arabic->setNumberFormat(1);
		$text = $Arabic->int2str($value);
		//echo "<center>$value<br />$text</center><hr />";


		//$Arabic->setNumberFormat(1); // حالة النصب أو الجر
		//$Arabic->setNumberOrder(2); // للترتيب

		return [
			"string"=>$text,
			"value"=>$value
		];
		
	}
	
    /**
     * Test
     */
    function getCommonMisspellings() {
		
	}

	/* -----------------------------------------------------------------------------
		Use IA to suggest an adequate game based on KNN
	----------------------------------------------------------------------------- */
    function suggestAGame($level, $errorFrequency, $rule) {
		
		$rules = [
			"ألف مقصورة-ممددوة" => 1,
			"ألف مقصورة-ياء" => 2,
			"الهمزة المتطرفة" => 3,
			"الهمزة المتوسطة" => 4,
			"تة" => 5,
			"تث" => 6,
			"تنوين" => 7,
			"ثس" => 8,
			"جق" => 9,
			"دذ" => 10,
			"ذز" => 11,
			"رز" => 12,
			"سش" => 13,
			"سص" => 14,
			"ظض" => 15,
			"ظط" => 16,
			"عغ" => 17,
			"قج" => 18,
			"كتابة الهمزة" => 19,
			"مد الضم" => 20,
			"مد الفتح" => 21,
			"مد الكسر" => 22,
			"مد تاء المخاطبة" => 23,
			"هة" => 24,
			"هح" => 25,
			"همزة القطع" => 26,
			"وصل ال" => 27,
			"وصل الواو" => 28,
		];
		
		$games = [
			"إكمال الفراغ" => 0,
			"تحرير جملة"  => 1,
			"تحرير كلمة"  => 2,
			"تحرير نص"  => 3,
			"ترتيب الحروف"  => 4,
			"ترتيب كلمة"  => 5,
			"سؤال ثقافي"  => 6,
			"سؤال مشوش"  => 7,
			"كتابة الساعة"  => 8,
			"كتابة عدد"  => 9,
			"مركب معجمي" => 10,
		];


		$dataset = new CsvDataset('dataset0.1.csv', 3, true);
		
		$samples = $dataset->getSamples();
		 foreach($samples as $key => $row) {
			$samples[$key][1] = $rules[$row[1]];
			$samples[$key][0] = floatval($samples[$key][0]);
			$samples[$key][2] = floatval($samples[$key][2]);
		 }
		 
		$labels = $dataset->getTargets();
		
		 
		 foreach($labels as $key => $label) {
			$labels[$key] = $games[$label];
		 }

		$classifier = new KNearestNeighbors();
		$classifier->train($samples, $labels);
		
		//$modelManager = new ModelManager();
		//$modelManager->saveToFile($classifier, "model");

		//$restoredClassifier = $modelManager->restoreFromFile("model");
		//$restoredClassifier->predict([6, 0.4 , $rules["تنوين"], 0]);

		$recommendation = $classifier->predict([$level, $errorFrequency, $rule]);
		return array_search ($recommendation, $games);

	}
	
	
}

