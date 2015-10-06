<?php
use \FunctionalTester as Tester;
use \Helper\Functional as Helper;

class UpdatesCest
{
    protected $source;
    protected $apiInfo;
    protected $apikey;
    protected $secret;
    protected $resourceURL;
    protected $nric;
    protected $amount;
    protected $date;
    protected $data;

    public function _before(Tester $I)
    {
        $this->source = 'cruise';
        $this->apiInfo = $I->retrieveUserInfo($this->source, \Helper\Functional::CSV_ORDER_SOURCE);
        $this->apikey = $this->apiInfo[Helper::CSV_ORDER_APIKEY];
        $this->secret = $this->apiInfo[Helper::CSV_ORDER_SHARED_SECRET];
        $this->resource=
        $this->resourceURL = '/api/updates/';
        $this->nric = 'S8888888P';
        $this->amount = '8.88';
        $this->date = date("Ymd");
        $this->data=Helper::assemblePostData($this->nric,$this->amount,$this->date,$this->source);
    }

    public function _after(Tester $I)
    {
        $date = date('Ym');
        $fileName = "misatravel_{$this->source}_{$date}.csv";
        $filePath="../../ntuc_files/$fileName";
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // tests
    public function checkAvailability(Tester $I)
    {
        $time = time();
        $I->wantTo('Check the Availability of updates service');
        $I->haveHttpHeader("apikey", $this->apikey);
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates", $this->data);
        $I->seeResponseCodeIs(200);
    }
    public function NoUnhandledError(Tester $I)
    {
        $time = time();
        $I->wantTo('Check is there any unhandled Error');
        $I->haveHttpHeader("apikey", $this->apikey);
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates", $this->data);
        $I->dontSeeResponseCodeIs(500);
    }
    public function MissingParam_1(Tester $I)
    {
        $time = time();
        $I->wantTo('Test missing apikey');
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates", $this->data);
        $I->seeResponseCodeIs(400);
    }
    public function MissingParam_2(Tester $I)
    {
        $time = time();
        $I->wantTo('Test missing timestamp');
        $I->haveHttpHeader("apikey", $this->apikey);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates", $this->data);
        $I->seeResponseCodeIs(400);
    }
    public function MissingParam_3(Tester $I)
    {
        $time = time();
        $I->wantTo('Test missing NRIC');
        $I->haveHttpHeader("apikey", $this->apikey);
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates", Helper::assemblePostData("",$this->amount,$this->date,$this->source));
        $I->seeResponseCodeIs(400);
    }
    public function MissingParam_4(Tester $I)
    {
        $time = time();
        $I->wantTo('Test missing amount');
        $I->haveHttpHeader("apikey", $this->apikey);
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates", Helper::assemblePostData($this->nric,"",$this->date,$this->source));
        $I->seeResponseCodeIs(400);
    }
    public function MissingParam_5(Tester $I)
    {
        $time = time();
        $I->wantTo('Test missing date');
        $I->haveHttpHeader("apikey", $this->apikey);
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates", Helper::assemblePostData($this->nric,$this->amount,"",$this->source));
        $I->seeResponseCodeIs(400);
    }
    public function MissingParam_6(Tester $I)
    {
        $time = time();
        $I->wantTo('Test missing source');
        $I->haveHttpHeader("apikey", $this->apikey);
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates",Helper::assemblePostData($this->nric,$this->amount,$this->date,""));
        $I->seeResponseCodeIs(400);
    }

    public function InvalidParam_1(Tester $I)
    {
        $time = time();
        $I->wantTo('Test invalid apikey');
        $I->haveHttpHeader("apikey", "blahblahblah");
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates", $this->data);
        $I->seeResponseCodeIs(401);
    }
    public function InvalidParam_2(Tester $I)
    {
        $time = date('Y-m-d');
        $I->wantTo('Test invalid timestamp');
        $I->haveHttpHeader("apikey", $this->apikey);
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates", $this->data);
        $I->seeResponseCodeIs(401);
    }
    public function InvalidParam_3(Tester $I)
    {
        $time = time();
        $I->wantTo('Test invalid NRIC');
        $I->haveHttpHeader("apikey", $this->apikey);
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates", Helper::assemblePostData("I am not NRIC",$this->amount,$this->date,$this->source));
        $I->seeResponseCodeIs(401);
    }
    public function InvalidParam_4(Tester $I)
    {
        $time = time();
        $I->wantTo('Test invalid amount');
        $I->haveHttpHeader("apikey", $this->apikey);
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates", Helper::assemblePostData($this->nric,"-0.12345abcd",$this->date,$this->source));
        $I->seeResponseCodeIs(401);
    }
    public function InvalidParam_5(Tester $I)
    {
        $time = time();
        $I->wantTo('Test invalid date');
        $I->haveHttpHeader("apikey", $this->apikey);
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates", Helper::assemblePostData($this->nric,$this->amount,"20150231",$this->source));
        $I->seeResponseCodeIs(401);
    }
    public function InvalidParam_6(Tester $I)
    {
        $time = time();
        $I->wantTo('Test invalid source');
        $I->haveHttpHeader("apikey", $this->apikey);
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendPOST("updates",Helper::assemblePostData($this->nric,$this->amount,$this->date,"airfares"));
        $I->seeResponseCodeIs(401);
    }

    public function OtherVerb(Tester $I)
    {
        $time = time();
        $I->wantTo('Test invalid Verb');
        $I->haveHttpHeader("apikey", $this->apikey);
        $I->haveHttpHeader("timestamp", $time);
        $I->haveHttpHeader("fingerprint", Helper::createFingerPrint($this->apikey, $this->secret, $time, 'POST', $this->resourceURL,$this->nric,$this->amount,$this->date,$this->source));
        $I->haveHttpHeader("Content-Type", 'application/x-www-form-urlencoded');
        $I->sendOPTIONS("updates",Helper::assemblePostData($this->nric,$this->amount,$this->date,$this->source));
        $I->seeResponseCodeIs(405);
    }
}
