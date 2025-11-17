<?php
declare(strict_types=1);

namespace Parking\Domain\Service;

use Parking\Domain\Interfaces\PricingStrategy;
use Parking\Domain\Service\CarPricingStrategy;
use Parking\Domain\Service\BikePricingStrategy;
use Parking\Domain\Service\TruckPricingStrategy;
use InvalidArgumentException;

class PricingStrategyFactory
{
    /**
     * @return PricingStrategy
     */
    public function getStrategy(string $vehicleType): PricingStrategy
    {
        $type = strtolower($vehicleType);

        switch ($type) {
            case 'carro':
                return new CarPricingStrategy();
            case 'moto':
                return new BikePricingStrategy();
            case 'caminhao':
                return new TruckPricingStrategy();
            default:
                throw new InvalidArgumentException("Tipo de veículo de tarifa desconhecido: " . $vehicleType);
        }
    }
}