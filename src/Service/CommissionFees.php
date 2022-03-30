<?php

declare(strict_types=1);

namespace fees\CommissionTask\Service;

use fees\CommissionTask\Service\{CurrencyExchangeRate, TransactionLineDataControl };


class CommissionFees {

    const DipositCom = 0.0003;
    const PrivateCom = 0.003;
    const BusinessCom = 0.005;
    const MAXFREEAMOUNT = 1000;
    const MAXTFREETRANSACTION = 3;

    public function __construct(array $transactions)
    {
        $this->transactions = $transactions;
    }

    // Round up the value with default 2 decimal precision ie : 0.023 -> 0.03

    private function round_up ( $value, $precision = 2 ) : float 
    { 
        $pow = pow ( 10, $precision ); 

        return ( ceil ( $pow * $value ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow; 
    } 

    public function getComissionSimpleRateValue(float $amount,float $commRate)
    {
  
            return  $this->round_up($amount*$commRate);

    }

    public function getComissionComplexRateValue(float $amount,float $prevSumAmount,int $nombreOfOperation,float $commRate)
    {

        if ($nombreOfOperation > self::MAXTFREETRANSACTION || $prevSumAmount > self::MAXFREEAMOUNT )
        {
            return $this->round_up($amount*$commRate);

        } elseif (($prevSumAmount+$amount) <= self::MAXFREEAMOUNT) {
            return 0;
        } else {
            return $this->round_up(($amount-(self::MAXFREEAMOUNT-$prevSumAmount))*$commRate);
        }

    }

    public function commissionOutput(CurrencyExchangeRate $currencyObj, array $currencyArray): array
    {

        $outPuArray = (array) null;
        $usersTransData = (array) null;

        $i = 0;

        foreach ($this->transactions as $line)
        {
            $transactionLineObj = new TransactionLineDataControl($line[0],$line[1],$line[2],$line[3],$line[4],$line[5]);

            $yearLastWeek = explode (",", $transactionLineObj->idOfWeek());

            $amount = $line[4]/($currencyObj->getExchangeRateValue($currencyArray,$line[5])) ;

            $key = false;

            if ( $transactionLineObj->isWithdraw() ) 
            {
                if ($transactionLineObj->isPrivateUser())
                {
                    if (!empty($usersTransData)) 
                    {
                        $key = array_search($line[1], array_column($usersTransData, 'user_id'), TRUE);
                    } 

                    if (FALSE !== $key )
                    { 
                        if (($usersTransData[$key]['year'] == $yearLastWeek[0]) && ($usersTransData[$key]['last_week'] == $yearLastWeek[1]))
                        {

                            $usersTransData[$key]['nt_week'] += 1;
                            $usersTransData[$key]['sum_amount'] += $amount;

                        } else {

                            $usersTransData[$key]['year'] = $yearLastWeek[0];
                            $usersTransData[$key]['last_week'] = $yearLastWeek[1];
                            $usersTransData[$key]['nt_week'] = 1;
                            $usersTransData[$key]['sum_amount'] = 0;

                        }

                        $usersTransData[$key]['amount'] = $amount;

                        $outPuArray[] = $this->getComissionComplexRateValue($usersTransData[$key]['amount'],$usersTransData[$key]['sum_amount'],$usersTransData[$key]['nt_week'],self::PrivateCom);

                    } else {

                        $usersTransData[$i]['user_id'] = $line[1];
                        $usersTransData[$i]['year'] = $yearLastWeek[0];
                        $usersTransData[$i]['last_week'] = $yearLastWeek[1];
                        $usersTransData[$i]['nt_week'] = 1;
                        $usersTransData[$i]['amount'] = $amount;
                        $usersTransData[$i]['sum_amount'] = 0;

                        $outPuArray[] = $this->getComissionComplexRateValue($usersTransData[$i]['amount'],$usersTransData[$i]['sum_amount'],$usersTransData[$i]['nt_week'],self::PrivateCom);

                        $i++;
                    }
                } elseif ($transactionLineObj->isBusinessUser())
                {
                    // CALCULATE AMOUNT COMMISSION 0.05

                    $outPuArray[] = $this->getComissionSimpleRateValue($amount,self::BusinessCom);

                }
            } elseif ( $transactionLineObj->isDeposit() )
            {
                $outPuArray[] = $this->getComissionSimpleRateValue($amount,self::DipositCom);
            }
        }

        return $outPuArray;
        
        }

    }
