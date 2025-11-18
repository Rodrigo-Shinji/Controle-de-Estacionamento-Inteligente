<?php
declare(strict_types=1);

namespace Parking\Application;

use Parking\Domain\Entity\ParkingRecord;
use Parking\Domain\Interfaces\ParkingRecordRepository;
use Parking\Domain\Service\PricingStrategyFactory; 
use Parking\Domain\Service\VehicleTypeValidator; 
use InvalidArgumentException;
use RuntimeException;
class CheckInService
{
    private ParkingRecordRepository $repository;
    private PricingStrategyFactory $strategyFactory;
    private VehicleTypeValidator $validator;

    public function __construct(
        ParkingRecordRepository $repository,
        PricingStrategyFactory $strategyFactory,
        VehicleTypeValidator $validator
    ) {
        $this->repository = $repository;
        $this->strategyFactory = $strategyFactory;
        $this->validator = $validator;
    }

    /**
     * @param array $input
     * @return ParkingRecord
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function execute(array $input): ParkingRecord
    {
        $errors = $this->validator->validate($input);
        if (!empty($errors)) {
            throw new InvalidArgumentException("Dados de entrada inválidos: " . implode(', ', $errors));
        }
        
        $plate = $input['plate'];
        $vehicleType = $input['vehicle_type'];

        if ($this->repository->findActiveByPlate($plate)) {
            throw new InvalidArgumentException("A placa **{$plate}** já está registrada e ainda não fez Check-Out. Não é possível realizar um novo Check-In.");
        }
        
        $strategy = $this->strategyFactory->getStrategy($vehicleType);
        $hourlyRate = $strategy->getHourlyRate();
        
        $record = ParkingRecord::createNew(
            $plate,
            $vehicleType,
            $hourlyRate
        );

        $this->repository->save($record);

        $savedRecord = $this->repository->findActiveByPlate($plate);

        if ($savedRecord === null) {
            throw new RuntimeException("Falha ao recuperar o registro após o Check-In. Verifique a conexão com o banco de dados.");
        }

        return $savedRecord;
    }
}