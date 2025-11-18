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
        
        $plateRegex = '/^([A-Z]{3}(\d{4}|\d{1}[A-Z]{1}\d{2}))$/'; 

        if (strlen($plate) !== 7 || !preg_match($plateRegex, $plate)) {
            $errors[] = 'Placa inválida! Utilize o formato de 7 caracteres alfanuméricos, sem hífens.';
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