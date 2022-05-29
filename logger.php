<?PHP

mb_internal_encoding("UTF-8");

error_reporting(E_ALL);
ini_set('display_errors', '1');


if(isset($_POST['level']) && isset($_POST['rule']) && isset($_POST['gravity']) && isset($_POST['gameid'])){

	// here treatement to analayze the feed
	// ...
	$level = floatval($_POST['level']);
	$errorFrequency = floatval($_POST['gravity']);
	$rule = floatval($_POST['rule']);
	$gameid = floatval($_POST['gameid']);
		
	// then log it, use SplFileObject
	$handle = fopen("feeding.csv", "a");
	fputcsv($handle, array($level, $errorFrequency, $rule, $gameid));
	
}

if(isset($_POST['input']) && isset($_POST['output']) && isset($_POST['json'])){

	function getClientIP(){
	  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	  } else {
		$ip = $_SERVER['REMOTE_ADDR'];
	  }
	  return $ip;
	}

	$ipaddress = getClientIP();

	function ip_details($ip) {
	  $json = file_get_contents("http://ip-api.com/json/{$ip}");
	  $details = json_decode($json, true);
	  return $details;
	}
	
	// In order to analayze the influence of the linguistic varieties
	$details = ip_details($ipaddress);
	$details['city'];

	$log = fopen('logs/requests.html', 'a');

	$time = date('H:i dS F');


	fwrite($log, 
		'<div>'.
		'<b>المدخل: </b>'.$_POST['input'].'<br/>'.
		'<b>معالجة: </b> نسبة الإتقان ('.$_POST['similarity'].'%)<br/>'.
		$_POST['output'].
		'<div style="text-align:left;direction:ltr;">'.
				'<b>Location: </b>'.$details['country'].' - '.$details['city'].'<br/>'.
		'</div>'.
		'</div>'.
		'<hr/>'.'<hr />'
	);

	fclose($log);

	echo "Logged";
}



exit;



?>
