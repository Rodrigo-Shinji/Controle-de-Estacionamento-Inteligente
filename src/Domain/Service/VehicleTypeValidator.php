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

        $normalizedPlate = str_replace('-', '', $plate);
        
        $plateRegex = '/^([A-Z]{3}\d{4})|([A-Z]{3}\d{1}[A-Z]{1}\d{2})$/'; 
        
        if (strlen($normalizedPlate) !== 7 || !preg_match($plateRegex, $normalizedPlate)) {
            $errors[] = 'Placa inválida! Utilize o formato de 7 caracteres alfanuméricos (Padrão Antigo ou Mercosul), com ou sem hífen (Ex: ABC-1234 ou ABC1B23).';
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