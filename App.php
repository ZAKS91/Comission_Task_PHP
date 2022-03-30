<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use fees\CommissionTask\Service\{CurrencyExchangeRate, TransactionFileCSV, CommissionFees};

// Load the transaction file

$csvFileObj = new TransactionFileCSV();
$transactionArray = $csvFileObj->getTransactionFileInArray();

// Load the currency exchange rate file

$currencyExchangeRateObj = new CurrencyExchangeRate('https://developers.paysera.com/tasks/api/currency-exchange-rates'); //('ExchageRateExemple.json');
$currencyArrayValues = $currencyExchangeRateObj->loadCurrentExchangeRateFile();

// Generate the commissions

$commissionObj = new CommissionFees($transactionArray);
$outPutCommissions = $commissionObj->commissionOutput($currencyExchangeRateObj,$currencyArrayValues);

print_r($outPutCommissions);

