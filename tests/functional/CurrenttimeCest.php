<?php
use \FunctionalTester;
use \Helper\Functional as Helper;

class CurrenttimeCest
{
    protected $apiInfo;

    public function _before(FunctionalTester $I)
    {
        $this->apiInfo = $I->retrieveUserInfo('cruise', \Helper\Functional::CSV_ORDER_SOURCE);
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests
    public function checkAvailability(FunctionalTester $I)
    {
        $I->wantTo('Check the Availability of Currenttime service');
        $I->haveHttpHeader("apikey", $this->apiInfo[Helper::CSV_ORDER_APIKEY]);
        $I->sendGET("currenttime", ["source" => $this->apiInfo[Helper::CSV_ORDER_SOURCE]]);
        $response = $I->grabResponse();
        $I->seeResponseCodeIs(200);
    }

    public function GetTimestamp(FunctionalTester $I)
    {
        $I->wantTo('Get Current Server Timestamp, make sure it is smaller than local');
        $I->haveHttpHeader("apikey", $this->apiInfo[Helper::CSV_ORDER_APIKEY]);
        $I->sendGET("currenttime", ["source" => $this->apiInfo[Helper::CSV_ORDER_SOURCE]]);
        $response = $I->grabResponse();
        usleep(10000);//0.01 sec
        $I->assertLessThan(time(), intval($response));
    }

    public function DetectInvalidApikeyError(FunctionalTester $I)
    {
        $I->wantToTest('whether it can detect an Invalid Apikey error');
        $I->sendGET("currenttime", ["source" => $this->apiInfo[Helper::CSV_ORDER_SOURCE]]);
        $I->seeResponseCodeIs(401);
    }

    public function DetectInvalidSourceError(FunctionalTester $I)
    {
        $I->wantToTest('whether it can detect an Invalid Source Error');
        $I->haveHttpHeader("apikey", $this->apiInfo[Helper::CSV_ORDER_APIKEY]);
        $I->sendGET("currenttime");
        $I->seeResponseCodeIs(401);
    }

    public function ResponseOfInvalidApikeyIsJson(FunctionalTester $I)
    {
        $I->wantToTest('response is JSON if we provide an Invalid Apikey');
        $I->haveHttpHeader("apikey", "blahblahblah");
        $I->sendGET("currenttime", ["source" => $this->apiInfo[Helper::CSV_ORDER_SOURCE]]);
        $I->seeResponseIsJson();
    }

    public function ResponseOfInvalidSourceIsJson(FunctionalTester $I)
    {
        $I->wantToTest('response is JSON if we provide an Invalid Source');
        $I->haveHttpHeader("apikey", $this->apiInfo[Helper::CSV_ORDER_APIKEY]);
        $I->sendGET("currenttime");
        $I->seeResponseIsJson();
    }

    public function ResponseOfInvalidApikey(FunctionalTester $I)
    {
        $I->wantToTest('response if we provide an Invalid Apikey');
        $I->haveHttpHeader("apikey", "blahblahblah");
        $I->sendGET("currenttime", ["source" => $this->apiInfo[Helper::CSV_ORDER_SOURCE]]);
        $I->assertEquals(json_decode('{"code": "1","message":"Please specify API key."}'),json_decode($I->grabResponse()));
    }

    public function ResponseOfInvalidSource(FunctionalTester $I)
    {
        $I->wantToTest('response if we provide an Invalid Source');
        $I->haveHttpHeader("apikey", $this->apiInfo[Helper::CSV_ORDER_APIKEY]);
        $I->sendGET("currenttime");
        $I->assertEquals(json_decode('{"code": "2","message":"Please specify source."}'),json_decode($I->grabResponse()));
    }
}
