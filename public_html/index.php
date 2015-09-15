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
	$fh = fopen(APIKEYS_DB_PATH,'r');
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



$app->post('/api/updates/', function () use($app){
	$apiKey = $app->request->headers->get('apikey');
	if (!strlen($apiKey)) {
		$app->halt(400,json_encode(array('status' => 0,'message' => 'Please specify API key')));
	}
	if (($csv = retrieveUserInfo($apiKey)) === FALSE) {
		$app->halt(401,json_encode(array('status' => 0,'message' => 'Invalid API key')));
	}
	$timestamp = $app->request->headers->get('timestamp');
	$fingerprint = $app->request->headers->get('fingerprint');
	if (!strlen($fingerprint)){
		$app->halt(400,json_encode(array('status' => 3,'message' => 'Please specify fingerprint')));
	}
	
	$timestamp =  intval($timestamp);
	$current = intval(time());
	$terms = 0;
	$tsB = $current - 90;
	$tsA =  $current + 90;
	
	
	do
		{
			if ($timestamp>$current)
			{	$diff = $timestamp - $current;
				$timestamp = $timestamp - $diff;
			}
			else if ($timestamp<$current)
			{	$diff = $current - $timestamp;
				$timestamp = $timestamp + $diff;
			}
			
			
		}while($timestamp<$tsB || $timestamp> $tsA);
	
    $request = $app->request;
    $nric = $request->post('nric');
    $amount = round($request->post('amount'),2);
    $source = $request->post('source');
   	$date = $request->post('date');
	
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
	
	

    $fd = fopen($title . ".csv", "a");
    $arr = array($nric, $date, $amount);
   	fputcsv($fd, $arr);
    fclose($fd);
	//$dd = date();
});

$app->run();

