<?php

declare(strict_types=1);

namespace fees\CommissionTask\Service;

class Math
{
    private $scale;

    public function __construct(int $scale)
    {
        $this->scale = $scale;
    }

    public function add(string $leftOperand, string $rightOperand): string
    {
        return bcadd($leftOperand, $rightOperand, $this->scale);
    }

    public function pow(string $num, string $exponent): string
    {
        return bcpow($num, $exponent, $this->scale);
    }
}


/*$Nombre = new Math(2);
$Resultat = $Nombre->add('12.25','13');

var_dump($Resultat);*/
