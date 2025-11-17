<?php
declare(strict_types=1);

namespace Parking\Domain\Service;

use Parking\Domain\Interfaces\PricingStrategy;
use DateTimeInterface;

abstract class AbstractHourlyPricingStrategy implements PricingStrategy
{
    protected function calculateTotalStrategy(DateTimeInterface $timeIn, DateTimeInterface $timeOut, float $rate): float
    {
        $diff = $timeOut->getTimestamp() - $timeIn->getTimestamp();
        
        $hoursFloat = $diff / 3600; 
        
        $chargedHours = ceil($hoursFloat);
        
        return $chargedHours * $rate;
    }
}