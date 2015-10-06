<?php
use \FunctionalTester as Tester;
use \Helper\Functional as Helper;

class CurrenttimeCest
{
    protected $apiInfo;

    public function _before(Tester $I)
    {
        //Retrieve API info from csv'
        $this->apiInfo = $I->retrieveUserInfo('cruise', \Helper\Functional::CSV_ORDER_SOURCE);
    }

    public function _after(Tester $I)
    {
    }

    // tests
    public function checkAvailability(Tester $I)
    {
        $I->wantTo('Check the Availability of Currenttime service');
        $I->haveHttpHeader("apikey", $this->apiInfo[Helper::CSV_ORDER_APIKEY]);
        $I->sendGET("currenttime", ["source" => $this->apiInfo[Helper::CSV_ORDER_SOURCE]]);
        $I->seeResponseCodeIs(200);
    }

    public function GetTimestamp(Tester $I)
    {
        $I->wantTo('Get Current Server Timestamp, make sure it is smaller than local');
        $I->haveHttpHeader("apikey", $this->apiInfo[Helper::CSV_ORDER_APIKEY]);
        $I->sendGET("currenttime", ["source" => $this->apiInfo[Helper::CSV_ORDER_SOURCE]]);
        $response = $I->grabResponse();
        $timeDifference=time()-intval($response);
        $I->assertTrue($timeDifference<90&&$timeDifference>-90);
    }

    public function DetectMissingApikeyError(Tester $I)
    {
        $I->wantToTest('whether it can detect an Invalid Apikey error');
        $I->sendGET("currenttime", ["source" => $this->apiInfo[Helper::CSV_ORDER_SOURCE]]);
        $I->seeResponseCodeIs(400);
    }

    public function DetectMissingSourceError(Tester $I)
    {
        $I->wantToTest('whether it can detect an Invalid Source Error');
        $I->haveHttpHeader("apikey", $this->apiInfo[Helper::CSV_ORDER_APIKEY]);
        $I->sendGET("currenttime");
        $I->seeResponseCodeIs(400);
    }

    public function ResponseOfInvalidApikeyIsJson(Tester $I)
    {
        $I->wantToTest('response is JSON if we provide an Invalid Apikey');
        $I->haveHttpHeader("apikey", "blahblahblah");
        $I->sendGET("currenttime", ["source" => $this->apiInfo[Helper::CSV_ORDER_SOURCE]]);
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
    }

    public function ResponseOfInvalidSourceIsJson(Tester $I)
    {
        $I->wantToTest('response is JSON if we provide an Invalid Source');
        $I->haveHttpHeader("apikey", $this->apiInfo[Helper::CSV_ORDER_APIKEY]);
        $I->sendGET("currenttime",["source"=>'blahblahblah']);
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
    }

    public function ResponseOfInvalidApikey(Tester $I)
    {
        $I->wantToTest('response if we provide an Invalid Apikey');
        $I->haveHttpHeader("apikey", "blahblahblah");
        $I->sendGET("currenttime", ["source" => $this->apiInfo[Helper::CSV_ORDER_SOURCE]]);
        $I->assertEquals(json_decode('{"code": "1","message":"Invalid API key."}'),json_decode($I->grabResponse()));
    }

    public function ResponseOfInvalidSource(Tester $I)
    {
        $I->wantToTest('response if we provide an Invalid Source');
        $I->haveHttpHeader("apikey", $this->apiInfo[Helper::CSV_ORDER_APIKEY]);
        $I->sendGET("currenttime",["source"=>'airfares']);//Not matching the API Key
        $I->assertEquals(json_decode('{"code": "2","message":"Invalid source."}'),json_decode($I->grabResponse()));
    }

    public function NotGetting500(Tester $I){
        $I->wantToTest('whether we are getting any 500 error');
        $I->haveHttpHeader("apikey", $this->apiInfo[Helper::CSV_ORDER_APIKEY]);
        $I->sendGET("currenttime",["source"=>$this->apiInfo[Helper::CSV_ORDER_SOURCE]]);
        $I->dontSeeResponseCodeIs(500);
    }
}
