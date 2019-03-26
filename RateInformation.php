<?php
namespace Rates;
class RateInformation
{
    /**
     * @param $source
     * @return array|string
     * @throws \Exception|\Error|\TypeError
     */
    public static function getData($source) {
        ini_set( 'serialize_precision', -1 );
        switch ($source) {
            case 'file':
                return self::rateFromFile();
            case 'api':
                return self::rateFromAPI();
            case 'random':
                return self::rateRandom();
            default:
                return "Invalid data source requested";
        }
    }

    /**
     * @return array
     */
    private static function rateFromFile() {
        $result = array();
        $data = file_get_contents('rates.dat');
        $data = str_replace("\n", "", $data);
        $data = str_replace("\r", "", $data);
        $data = explode(';', $data);
        foreach ($data as &$item) {
            if ("" !== $item) {
                $item = explode(' ', $item);
                $result = array_merge($result, array($item[0] => round(floatval($item[1]), 2)));
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private static function rateFromAPI() {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5',
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
        ));
        $result = array();
        $response = curl_exec($curl);
        if (self::isValidJSON($response) && strlen($response) > 0) {
            $response = json_decode($response, true);
            foreach ($response as &$currency) {
                if ('USD' === $currency['ccy'] || 'EUR' === $currency['ccy']) {
                    $result[strtolower($currency['ccy'])] = round($currency['buy'], 2);
                }
            }
        }

        return $result;
    }

    /**
     * @return array|string
     * @throws \Exception|\Error|\TypeError
     */
    private static function rateRandom() {
        try {
            return array(
                'usd' => round(self::randomFloat(25, 29), 2),
                'eur' => round(self::randomFloat(28, 31), 2)
            );
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $min
     * @param $max
     * @return float|int
     * @throws \Exception|\Error|\TypeError
     */
    private static function randomFloat($min, $max) {
        try {
            return random_int($min, $max - 1) + (random_int(0, 100) / 100);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $str
     * @return bool
     */
    private static function isValidJSON($str) {
        json_decode($str, true);

        return JSON_ERROR_NONE === json_last_error();
    }
}