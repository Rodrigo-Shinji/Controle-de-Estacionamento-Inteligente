<?php

namespace App\Domain\Repository;

use App\Domain\Entity\ParkingRecord;

interface ParkingRecordRepository
{
    public function save(ParkingRecord $record): void;

    public function findOpenByPlate(string $plate): ?ParkingRecord;

    public function update(ParkingRecord $record): void;

    public function getReport(): array;
}
