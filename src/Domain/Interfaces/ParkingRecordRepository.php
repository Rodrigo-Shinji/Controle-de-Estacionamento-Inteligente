<?php
declare(strict_types=1);

namespace Parking\Domain\Interfaces;

use Parking\Domain\Entity\ParkingRecord;

interface ParkingRecordRepository
{
    public function save(ParkingRecord $record): void;
    public function findById(int $id): ?ParkingRecord;
    public function findAll(): array;
    public function findActiveByPlate(string $plate): ?ParkingRecord;
}