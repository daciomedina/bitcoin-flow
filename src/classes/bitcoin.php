<?php
include "curlapi.php";

class Bitcoin {

    const API_URL = "https://api.coindesk.com/v1/bpi/currentprice.json";
    public $bitcoinApi;
    public $apiResponse;
    public function __construct()
    {
        $this->bitcoinApi = new CurlApi(self::API_URL);
    }

    public function getCurrentPrice(String $countryIso)
    {
        $responsePrice = $this->bitcoinApi->callAPI('bitcoin','GET');
        return (($responsePrice['statusCode'] === 200) ? json_decode($responsePrice['response'],true)['bpi'][$countryIso]['rate_float'] : '');
    }

}
