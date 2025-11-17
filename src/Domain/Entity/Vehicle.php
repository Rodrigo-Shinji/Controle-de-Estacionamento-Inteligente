<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use Parking\Domain\Constants\VehicleTypeConstant;
use InvalidArgumentException;

class Vehicle
{
    private string $plate;
    private string $type;

    public function __construct(string $plate, string $type)
    {

        $this->plate = strtoupper(trim($plate));

        if (!VehicleTypeConstant::isValid($type)) {
            throw new InvalidArgumentException("Tipo de veículo inválido: '{$type}'. Deve ser um dos tipos definidos.");
        }
        $this->type = strtolower(trim($type));
    }

    public function plate(): string
    {
        return $this->plate;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function equals(Vehicle $other): bool
    {
        return $this->plate === $other->plate();
    }
}