<?php
declare(strict_types=1);

namespace Parking\Domain\Service;

use DateTimeInterface;

class SavedRatePricingStrategy extends AbstractHourlyPricingStrategy
{
    private float $rate;

    public function __construct(float $rate)
    {
        $this->rate = $rate;
    }

    public function getHourlyRate(): float
    {
        return $this->rate;
    }

    public function calculateTotal(DateTimeInterface $timeIn, DateTimeInterface $timeOut): float
    {
        return $this->calculateTotalStrategy($timeIn, $timeOut, $this->rate);
    }
}