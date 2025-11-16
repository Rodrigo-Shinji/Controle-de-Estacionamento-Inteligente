<?php
declare(strict_types=1);

namespace Parking\Domain\Entity;

use DateTimeImmutable;

class ParkingRecord
{
    private ?int $id;
    private string $plate;
    private string $vehicleType;
    private DateTimeImmutable $timeIn;
    private ?DateTimeImmutable $timeOut;
    private float $hourlyRate;
    private ?float $totalFare;

    public function __construct(
        ?int $id,
        string $plate,
        string $vehicleType,
        DateTimeImmutable $timeIn,
        float $hourlyRate,
        ?DateTimeImmutable $timeOut = null,
        ?float $totalFare = null
    ) {
        $this->id = $id;
        $this->plate = $plate;
        $this->vehicleType = $vehicleType;
        $this->timeIn = $timeIn;
        $this->timeOut = $timeOut;
        $this->hourlyRate = $hourlyRate;
        $this->totalFare = $totalFare;
    }


    public static function createNew(
        string $plate, 
        string $vehicleType, 
        float $hourlyRate
    ): self {
        return new self(
            null, 
            $plate,
            $vehicleType,
            new DateTimeImmutable(), 
            $hourlyRate
        );
    }
    
    public function checkout(float $totalFare): void
    {
        if ($this->timeOut !== null) {
            throw new \RuntimeException("O veículo já fez o Check-Out.");
        }
        
        $this->timeOut = new DateTimeImmutable();
        $this->totalFare = $totalFare;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlate(): string
    {
        return $this->plate;
    }

    public function getVehicleType(): string
    {
        return $this->vehicleType;
    }

    public function getTimeIn(): DateTimeImmutable
    {
        return $this->timeIn;
    }

    public function getTimeOut(): ?DateTimeImmutable
    {
        return $this->timeOut;
    }
    
    public function getHourlyRate(): float
    {
        return $this->hourlyRate;
    }

    public function getTotalFare(): ?float
    {
        return $this->totalFare;
    }
    
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}