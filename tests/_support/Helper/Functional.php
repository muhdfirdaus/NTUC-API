<?php
namespace Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Functional extends \Codeception\Module
{
    const APIKEYS_DB_PATH = 'apikeys/apikeys.csv';
    const CSV_ORDER_APIKEY=0;
    const CSV_ORDER_SHARED_SECRET=1;
    const CSV_ORDER_SOURCE=2;

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
    protected static function assembleFingerprintData($nric, $amount, $date, $source)
    {
        return "nric=$nric&amount=$amount&date=$date&source=$source";
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
        $fingerPrint = calculateFingerprint($apikey, $secret, $timestamp, $method, $resourceUri, self::assembleFingerprintData($nric, $amount, $date, $source));
        return $fingerPrint;
    }

    public static function retrieveUserInfo($queryValue, $column=self::CSV_ORDER_APIKEY)
    {
        $apikeyFile = fopen(APIKEYS_DB_PATH, 'r');
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

    public static function retrieveSharedSecret($apikey)
    {
        return retrieveUserInfo($apikey)[self::CSV_ORDER_SHARED_SECRET];
    }

    public static function retrieveSource($apikey)
    {
        return retrieveUserInfo($apikey)[self::CSV_ORDER_SOURCE];
    }
}
