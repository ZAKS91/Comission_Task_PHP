<?php

declare(strict_types=1);

namespace fees\CommissionTask\Tests\Service;

use PHPUnit\Framework\TestCase;
use fees\CommissionTask\Service\{CurrencyExchangeRate,CommissionFees};

class CommissionFeesTest extends TestCase
{
    private $transactionFile = [
        ['2014-12-31', '4', 'private', 'withdraw', '1200.00', 'EUR'],
        ['2015-01-01', '4', 'private', 'withdraw', '1000.00', 'EUR'],
        ['2016-01-05', '4', 'private', 'withdraw', '1000.00', 'EUR'],
        ['2016-01-05', '1', 'private', 'deposit', '200.00', 'EUR'],
        ['2016-01-06', '2', 'business', 'withdraw', '300.00', 'EUR'],
    ];

    private $exchangeRatePath = 'ExchageRateExemple.json';

    private $expectedResult = [0.6,3.0,0,0.06,1.5];

    /**
     * @var CommissionFees
     */
    private $commissionFees;

    /**
     * @var CurrencyExchangeRate
     */
    private $currencyExchangeRate;


    public function setUp()
    {
        $this->commissionFees = new CommissionFees($this->transactionFile);
        $this->currencyExchangeRate = new currencyExchangeRate($this->exchangeRatePath);
    }

    public function testCommissionOutputrray()
    {

        $this->assertSame($this->expectedResult,$this->commissionFees->commissionOutput($this->currencyExchangeRate,$this->currencyExchangeRate->loadCurrentExchangeRateFile()));

    }

    public function dataProviderForAddTesting(): array
    {
        return [
            '2014-12-31,4,private,withdraw,1200.00,EUR' => ['2014-12-31', '4', 'private', 'withdraw', '1200.00', 'EUR', '0.60'],
            '2015-01-01,4,private,withdraw,1000.00,EUR' => ['2015-01-01', '4', 'private', 'withdraw', '1000.00', 'EUR', '3.00'],
            '2016-01-05,4,private,withdraw,1000.00,EUR' => ['2016-01-05', '4', 'private', 'withdraw', '1000.00', 'EUR', '0.00'],
            '2016-01-05,1,private,deposit,200.00,EUR' => ['2016-01-05', '1', 'private', 'deposit', '200.00', 'EUR', '0.06'],
            '2016-01-06,2,business,withdraw,300.00,EUR' => ['2016-01-06', '2', 'business', 'withdraw', '300.00', 'EUR', '1.50'],
        ];
    }
}