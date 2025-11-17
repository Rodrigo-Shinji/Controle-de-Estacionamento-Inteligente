<?php 
declare(strict_types=1);

namespace Parking\Infra\Repository;

use Parking\Domain\Interfaces\ParkingRecordRepository;
use Parking\Domain\Entity\ParkingRecord; 
use Parking\Infra\Database\SQLiteConnection;
use \PDO;
use DateTimeImmutable;

class SQLiteParkingRecordRepository implements ParkingRecordRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = SQLiteConnection::connect();
    }

    public function save(ParkingRecord $record): void
    { 
        $sql = "INSERT INTO passages (plate, vehicle_type, time_in, time_out, hourly_rate, total_fare) 
                 VALUES (:plate, :vehicle_type, :time_in, :time_out, :hourly_rate, :total_fare)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':plate' => $record->getPlate(),
            ':vehicle_type' => $record->getVehicleType(),
            ':time_in' => $record->getTimeIn()->format('Y-m-d H:i:s'),
            ':time_out' => $record->getTimeOut() ? $record->getTimeOut()->format('Y-m-d H:i:s') : null,
            ':hourly_rate' => $record->getHourlyRate(),
            ':total_fare' => $record->getTotalFare(),
        ]);

    }
        
    public function findById(int $id): ?ParkingRecord
    {
        $sql = "SELECT * FROM passages WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapToEntity($data);
    }
    
    public function findAll(): array
    {
        $sql = "SELECT * FROM passages ORDER BY time_in DESC";
        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $records = [];
        foreach ($results as $data) {
            $records[] = $this->mapToEntity($data);
        }
        return $records;
    }

    private function mapToEntity(array $data): ParkingRecord
    {
        $timeOut = $data['time_out'] ? new DateTimeImmutable($data['time_out']) : null;
        
        return new ParkingRecord(
            (int)$data['id'],
            $data['plate'],
            $data['vehicle_type'],
            new DateTimeImmutable($data['time_in']),
            (float)$data['hourly_rate'],
            $timeOut,
            $data['total_fare'] !== null ? (float)$data['total_fare'] : null
        );
    }

    public function findActiveByPlate(string $plate): ?ParkingRecord
    {
        $sql = "SELECT * FROM passages WHERE plate = :plate AND time_out IS NULL ORDER BY time_in DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':plate' => $plate]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapToEntity($data);
    }
}