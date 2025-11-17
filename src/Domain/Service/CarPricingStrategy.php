<?php
declare(strict_types=1);

namespace Parking\Domain\Service;

use DateTimeInterface;

class CarPricingStrategy extends AbstractHourlyPricingStrategy
{
    private const HOURLY_RATE = 5.00; 

    public function getHourlyRate(): float
    {
        return self::HOURLY_RATE;
    }

    public function calculateTotal(DateTimeInterface $timeIn, DateTimeInterface $timeOut): float
    {
        return $this->calculateTotalStrategy($timeIn, $timeOut, self::HOURLY_RATE);
    }
}