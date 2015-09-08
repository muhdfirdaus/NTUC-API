<?php 
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
//$app->post('/hello/:name', function ($p1)  {
  //  echo "byebye, " . $p1 . " <br>";
	
//});
$app->get('/api/getcurrenttime', 'aa');

function aa() {
   $date = time();
   echo $date."<br>";
   //return $date;
   //$date1 = date('i:s',$date);
   //echo $date1;
};
$app->post('/api/updates/', function () use($app){
	$apiKey = $app->request->headers->get('api_key');
	$timestamp = $app->request->headers->get('timestamp');
	$fingerprint = $app->request->headers->get('fingerprint');
	$timestamp =  intval($timestamp);
	$current = intval(time());
	
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
	{echo  "Error on date! \n";}
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
	$dd = date();

});

$app->run();

