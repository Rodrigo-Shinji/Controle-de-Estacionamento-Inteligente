<?php
declare(strict_types=1);

namespace Parking\Application\Service;

use Parking\Domain\Interfaces\ParkingRecordRepository;
use Parking\Domain\Service\ParkingPriceCalculator;
use Parking\Domain\Service\SavedRatePricingStrategy;
use Parking\Domain\Entity\ParkingRecord;
use DateTimeImmutable;
use RuntimeException;

class CheckOutService
{
    private ParkingRecordRepository $repository;

    public function __construct(ParkingRecordRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $plate
     * @param DateTimeImmutable $timeOut
     * @return ParkingRecord
     * @throws RuntimeException
     */
    public function execute(string $plate, DateTimeImmutable $timeOut): ParkingRecord
    {
        $record = $this->repository->findActiveByPlate($plate); 
        
        if ($record === null) {
            throw new RuntimeException("Nenhum veículo ativo encontrado com a placa: " . $plate);
        }

        $rate = $record->getHourlyRate();
        $strategy = new SavedRatePricingStrategy($rate);
        
        $calculator = new ParkingPriceCalculator($strategy); 

        $totalFare = $calculator->calculate($record->getTimeIn(), $timeOut);

        $record->checkout($totalFare, $timeOut);

        $this->repository->save($record);

        return $record;
    }
}