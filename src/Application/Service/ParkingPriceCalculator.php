<?php
declare(strict_types=1);    

namespace Parking\Application\Service;

use Parking\Domain\Interfaces\PricingStrategy;
use DateTimeInterface;

class ParkingPriceCalculator
{
    private PricingStrategy $strategy;

    /**
     * @param PricingStrategy $strategy
     */
    public function __construct(PricingStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @param DateTimeInterface $timeIn
     * @param DateTimeInterface $timeOut
     * @return float
     */
    public function calculate(DateTimeInterface $timeIn, DateTimeInterface $timeOut): float
    {
        return $this->strategy->calculateTotal($timeIn, $timeOut);
    }
}