<?php
declare(strict_types=1);

namespace App\Domain\Constants;

final class VehicleTypeConstant
{
    public const CARRO = 'carro';
    public const MOTO = 'moto';
    public const CAMINHAO = 'caminhao';

    /**
     *@return string[]
     */
    public static function getTypes(): array
    {
        return [self::CAR, self::MOTO, self::TRUCK];
    }

    public static function isValid(string $type): bool
    {
        return in_array(strtolower(trim($type)), self::getTypes(), true);
    }
}