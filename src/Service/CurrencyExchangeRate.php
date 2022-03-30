<?php

/* 

    For exchange Rate data two solutions are possible. 
    
    1- Loading the contents of the file in memory  and execute the processing of transactions with this file 
    2- or , fetch the exchange data at each transaction directly on the online file. 
    
    (in this solution I favored the fast access to the data by loading the file in memory).
    
*/

declare(strict_types=1);

namespace fees\CommissionTask\Service;

class CurrencyExchangeRate
{
    private  $currencyExchangeUrl;

    public function __construct(string $currencyExchangeUrl)
    {
        $this->currencyExchangeUrl = $currencyExchangeUrl;
    }

    public function loadCurrentExchangeRateFile () 
    {
        
        if (($json = file_get_contents($this->currencyExchangeUrl)) === false) {
            $error = error_get_last();
            trigger_error('HTTP request failed. Error was: ' . $error['message'], E_USER_ERROR);
        } 
           
            $json = file_get_contents($this->currencyExchangeUrl);
            return array_values(json_decode($json, true));
   
    }

    public function getExchangeRateValue(array $currencyArray,string $currency)
    {
        return array_column($currencyArray,$currency)[0];
    }
}


