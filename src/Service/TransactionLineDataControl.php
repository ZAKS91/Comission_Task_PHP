<?php

declare(strict_types=1);

namespace fees\CommissionTask\Service;

use DateTime;

class TransactionLineDataControl
{
    /**
     * @var string
     */
    public $date;

    /**
     * @var string
     */
    public $userId;

    /**
     * @var string
     */
    public $userType;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $amount;

    /**
     * @var string
     */
    public $baseAmount;

    /**
     * @var string
     */
    public $currency;

    public function __construct(
        string $date,
        string $userId,
        string $userType,
        string $type,
        string $amount,
        string $currency
    ) {
        $this->date = $date;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->type = $type;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function isDeposit(): bool
    {
        return $this->type === 'deposit';
    }

    public function isWithdraw(): bool
    {
        return $this->type === 'withdraw';
    }

    public function isPrivateUser(): bool
    {
        return $this->userType === 'private';
    }

    public function isBusinessUser(): bool
    {
        return $this->userType === 'business';
    }

    public function idOfWeek(): string
    {
        $datetime = new DateTime($this->date);
        $year = (int) $datetime->format('Y');

        // get rid of the last week of year

        $lastWeekStartDate = (new \DateTime())->setISODate($year, 53);
        if ($lastWeekStartDate->diff($datetime)->days < 6) {
            ++$year;
        }

        return $year.",".$datetime->format('W');
    }
}
