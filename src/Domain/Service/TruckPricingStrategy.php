<?php
declare(strict_types=1);    

namespace Parking\Domain\Pricing;

use Parking\Domain\Interfaces\PricingStrategy;
use DateTimeInterface;

class CarPricingStrategy implements PricingStrategy
{
    private const HOURLY_RATE = 10.00;

    public function getHourlyRate(): float
    {
        return self::HOURLY_RATE;
    }

    public function calculateTotal(DateTimeInterface $timeIn, DateTimeInterface $timeOut): float
    {
        return $this->calculateTotalStrategy($timeIn, $timeOut, self::HOURLY_RATE);
    }
    
    private function calculateTotalStrategy(DateTimeInterface $timeIn, DateTimeInterface $timeOut, float $rate): float
    {
        $diff = $timeOut->getTimestamp() - $timeIn->getTimestamp();
        
        $hoursFloat = $diff / 3600; 
        
        $chargedHours = ceil($hoursFloat);
        
        return $chargedHours * $rate;
    }
}