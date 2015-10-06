<?php
namespace Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Functional extends \Codeception\Module
{
    const APIKEYS_DB_PATH = 'apikeys/apikeys.csv';
    const CSV_ORDER_APIKEY = 0;
    const CSV_ORDER_SHARED_SECRET = 1;
    const CSV_ORDER_SOURCE = 2;

    /**
     * @param string $data
     * @return string sha256 hash string
     */
    public static function sha256($data)
    {
        return hash('sha256', $data);
    }

    /**
     * @param string $apikey
     * @param string $secret
     * @param string|int $timestamp
     * @param string $method
     * @param string $resourceUri
     * @param string $data
     * @return string Fingerprint
     */
    protected static function computeFingerprint($apikey, $secret, $timestamp, $method, $resourceUri, $data)
    {
        return self::sha256("$apikey,$secret,$timestamp,$method,$resourceUri,$data");
    }

    /**
     * @param string $nric
     * @param string|float $amount
     * @param string $date
     * @param string $source
     * @return string
     */
    public static function assemblePostData($nric, $amount, $date, $source)
    {
        $data = ['nric' => $nric, 'amount' => $amount, 'date' => $date, 'source' => $source];
        return http_build_query($data);
    }

    /**
     * Note: This parameters order is differ from the original function in index.php
     * @param string $apikey
     * @param string $secret
     * @param string|int $timestamp
     * @param string $method
     * @param string $resourceUri
     * @param string $nric
     * @param string|float $amount
     * @param string $date
     * @param string $source
     * @return string
     */
    public static function createFingerPrint($apikey, $secret, $timestamp, $method, $resourceUri, $nric, $amount, $date, $source)
    {
        $fingerPrint = self::computeFingerprint($apikey, $secret, $timestamp, $method, $resourceUri, self::assemblePostData($nric, $amount, $date, $source));
        return $fingerPrint;
    }

    /**
     * Allow to search particular API info using different column
     * @param string $queryValue the value of the selected column, no blur search is allowed.
     * @param int $column pass in a constant int started with CSV_ORDER_
     * @return array|bool
     */
    public static function retrieveUserInfo($queryValue, $column = self::CSV_ORDER_APIKEY)
    {
        $apikeyFile = fopen(self::APIKEYS_DB_PATH, 'r');
        try {
            do {
                $csvRecord = fgetcsv($apikeyFile);
                if (!strcmp($queryValue, $csvRecord[$column])) {
                    return $csvRecord;
                }
            } while ($csvRecord !== FALSE);
        } finally {
            fclose($apikeyFile);
        }
        return FALSE;
    }
}
