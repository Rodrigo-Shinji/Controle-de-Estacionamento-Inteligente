<?php
declare(strict_types=1);

final class VehicleValidator
{
    /**
     * @param array{
     * vehicle_type?:string,
     * plate?:string,
     * time?:string,
     * } $input
     * @return string[]
     */

    public function validate(array $input): array{
        $errors = [];

        $type = strtolower(trim((string)($input['vehicle_type'] ?? '')));
        $plate = strtoupper(trim((string)($input['plate'] ?? '')));

        if ($plate == '' || !preg_match('/^[A-Z0-9-]{5,10}$/', $plate)){
            $erros[] = 'Placa inválida!';
        }

        $allowed = ['carro','moto','caminhao'];
        if (!in_array($type, $allowed, true)){
            $errors[] = 'Tipo de veículo inválido!';
        }

        if (\DateTime::createFromFormat(\DateTime::ATOM, $when) === false){
            $errors[] = 'Data/Hora inválida';
        }

        return $errors;
    }
}






?>