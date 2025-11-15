<?php

namespace App\Domain\Entity;

use App\Domain\Constants\ValueObject\VehicleTypeConstant;

class ParkingRecord
{
    private ?int $id;
    private string $plate;
    private string $type;
    private \DateTimeImmutable $entryTime;
    private ?\DateTimeImmutable $exitTime = null;
    private ?float $price = null;

    public function __construct(
        ?int $id,
        string $plate,
        string $type,
        \DateTimeImmutable $entryTime,
        ?\DateTimeImmutable $exitTime = null,
        ?float $price = null
    ) {
        if (!VehicleTypeConstant::isValid($type)) {
            throw new \InvalidArgumentException("Tipo de veículo inválido.");
        }

        $this->id = $id;
        $this->plate = $plate;
        $this->type = strtolower(trim($type));
        $this->entryTime = $entryTime;
        $this->exitTime = $exitTime;
        $this->price = $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlate(): string
    {
        return $this->plate;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getEntryTime(): \DateTimeImmutable
    {
        return $this->entryTime;
    }

    public function getExitTime(): ?\DateTimeImmutable
    {
        return $this->exitTime;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function markExit(\DateTimeImmutable $exitTime, float $price): void
    {
        $this->exitTime = $exitTime;
        $this->price = $price;
    }
}
