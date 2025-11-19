<?php
declare(strict_types=1);

namespace Parking\Domain\Service;

use Parking\Domain\Interfaces\PricingStrategy;
use DateTimeInterface;

class ParkingPriceCalculator
{
    private PricingStrategy $strategy;

    public function __construct(PricingStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @return float
     */
    public function calculate(DateTimeInterface $timeIn, DateTimeInterface $timeOut): float
    {
        return $this->strategy->calculateTotal($timeIn, $timeOut);
    }

}
