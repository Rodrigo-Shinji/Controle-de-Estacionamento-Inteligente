<?php
declare(strict_types=1);

namespace Parking\Domain\Interfaces;

use DateTimeInterface;

interface PricingStrategy
{

    public function getHourlyRate(): float;

    /**
     * @param DateTimeInterface $timeIn horário de entrada 
     * @param DateTimeInterface $timeOut horário de saída
     * @return float
     */
    public function calculateTotal(DateTimeInterface $timeIn, DateTimeInterface $timeOut): float;
}