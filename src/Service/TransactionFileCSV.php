<?php

declare(strict_types=1);

namespace fees\CommissionTask\Service;

class TransactionFileCSV
{
    public function getTransactionFileInArray(){
        try {
            $filleName = getopt('f:');
            $fileHandle = fopen($filleName['f'], 'r');

            while (! feof($fileHandle) ) {
                $line[] = fgetcsv($fileHandle, 1024);
            }
        } finally  {
            fclose($fileHandle);
            return $line;
        }
    }

}


