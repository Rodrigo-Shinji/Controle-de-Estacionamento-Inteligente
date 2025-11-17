<?php
declare(strict_types=1);

namespace Parking\Domain\Service;

use Parking\Domain\Constants\VehicleTypeConstant;

final class VehicleTypeValidator
{
    /**
     * @param array{
     * vehicle_type?:string,
     * plate?:string,
     * time?:string,
     * }$input
     * @return string[]
     */
    public function validate(array $input): array
    {
        $errors = [];

        $type = strtolower(trim((string)($input['vehicle_type'] ?? '')));
        $plate = strtoupper(trim((string)($input['plate'] ?? '')));
        $when = (string)($input['time'] ?? '');

        if ($plate == '' || !preg_match('/^[A-Z0-9-]{5,10}$/', $plate)) {
            $errors[] = 'Placa inválida!';
        }

        if (!VehicleTypeConstant::isValid($type)) {
            $errors[] = 'Tipo de veículo inválido!';
        }

        if (\DateTime::createFromFormat(\DateTime::ATOM, $when) === false){
            $errors[] = 'Data/Hora inválida';
        }

        return $errors;
    }
}