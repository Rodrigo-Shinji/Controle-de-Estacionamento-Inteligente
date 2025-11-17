<?php
declare(strict_types=1);

namespace Parking\Application\Service;

use Parking\Domain\Entity\ParkingRecord;
use Parking\Domain\Interfaces\ParkingRecordRepository;
use Parking\Domain\Service\PricingStrategyFactory; 
use Parking\Domain\Service\VehicleTypeValidator; 
use InvalidArgumentException;

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
     */
    public function execute(array $input): ParkingRecord
    {
        $errors = $this->validator->validate($input);
        if (!empty($errors)) {
            throw new InvalidArgumentException("Dados de entrada inválidos: " . implode(', ', $errors));
        }
        
        $plate = $input['plate'];
        $vehicleType = $input['vehicle_type'];

        $strategy = $this->strategyFactory->getStrategy($vehicleType);
        $hourlyRate = $strategy->getHourlyRate();
        
        $record = ParkingRecord::createNew(
            $plate,
            $vehicleType,
            $hourlyRate
        );

        $this->repository->save($record);

        return $record;
    }
}