<?php

	define('APIKEYS_DB_PATH','../apikeys/apikeys.csv');

	require '../vendor/autoload.php';

$app = new \Slim\Slim();
$app->get('/hello/:name/:surname', function ($p1,$p2) {
	echo "What, " . $p1 . " " . $p2 . "<br>";
	echo hash('sha256', 'test');

});

$app->get('/hello/:name', function ($p1) use(&$aa){
	echo "hello, " . $p1 . " <br>";
	echo hash('sha256', 'Test.');
	$tt = aa();
	echo $tt;

});


$app->get('/api/getcurrenttime', 'aa');

function aa() {
   $date = time();
   echo $date;
   
};

function retrieveUserInfo($apikey) {
	$fh = fopen('C://xampp/htdocs/ntuc/apikeys/apikeys.csv','r');
	try {
		do {
			$csv = fgetcsv($fh);
			if (!strcmp($apikey,$csv[0])) {
				return $csv;
			}
		} while($csv !== FALSE);
	} finally {
		fclose($fh);
	}
	return FALSE;
}


function fp($apikey, $timestamp, $nric, $amount, $date, $source){
		$fp = hash('sha256', $apikey. "," .$timestamp.",POST,api/updates,nric=".$nric."&amount=".$amount."&date=".$date."&source=".$source );
		return $fp;
}


$app->get('/api/getfp', 'ab');

	function ab() {
		$timestamp = time();
		$f = hash('sha256', "12345," .$timestamp.",POST,api/updates,nric=nn123&amount=12345.12&date=20150917&source=cruise" );
		echo $timestamp."->>".$f;
		
		 
};



$app->post('/api/updates/', function () use($app){
		$apiKey = $app->request->headers->get('apikey');
		if (!strlen($apiKey)) {
			$app->halt(400,json_encode(array('status' => 0,'message' => 'Please specify API key')));
		}
		if (($csv = retrieveUserInfo($apiKey)) === FALSE) {
			$app->halt(401,json_encode(array('status' => 0,'message' => 'Invalid API key')));
		}

		$timestamp = $app->request->headers->get('timestamp');

		if (!strlen($timestamp)) {
			$app->halt(400,json_encode(array('status' => 2,'message' => 'Please specify Timestamp')));
		}

		$fingerprint = $app->request->headers->get('fingerprint');
		if (!strlen($fingerprint)){

			//$app->halt(401,json_encode(array('status' => 2,'message' => 'Invalid fingerprint')));

			$app->halt(400,json_encode(array('status' => 3,'message' => 'Please specify fingerprint')));
			}

		

			
		$timestamp =  intval($timestamp);
		//$current = intval(time());
		$terms = 0;
		$tsB = $timestamp - 90;
		$tsA =  $timestamp + 90;


		$request = $app->request;
		$nric = $request->post('nric');
		$amount = round($request->post('amount'),2);
		$source = $request->post('source');
		$date = $request->post('date');
		
		$fp = fp($apiKey, $timestamp, $nric, $amount, $date, $source);
		
		if ($fp == $fingerprint){
			
		if ($timestamp>=$tsB && $timestamp<=$tsA)
		{

			$d = $date[6] . $date[7];
			$m = $date[4] . $date[5];
			$y = $date[0] . $date[1] . $date[2] . $date[3];

			if ($d>31 || $m>12)
			{echo  "Error on date! \n";
			}
			if($d>=25)
			{
				if($m==12)
				{
					$y = $y + 1;
					$m = "01";
				}
				elseif($m<12)
				{$m = $m + 1;
				$m = "0". $m;}

			}

			$titleD =  $y.$m;
			$title = "misatravel_" . $source . "_" . $titleD;




			
			$arr = array($nric, $date, $amount);
			fputcsv($fd, $arr);
			fclose($fd);
			echo 0;
		}
		else
			$app->halt(401,json_encode(array('status' => 3,'message' => 'Invalid Timestamp')));

		}
		else if ($fp!=$fingerprint)
			{
			$app->halt(401,json_encode(array('status' => 2,'message' => 'Invalid fingerprint')));}


	});
	$app->run();
