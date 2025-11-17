<?php
declare(strict_types=1);

namespace Parking\Application\Service;

use Parking\Domain\Interfaces\ParkingRecordRepository;

class ReportService
{
    private ParkingRecordRepository $repository;

    public function __construct(ParkingRecordRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function generateFaturamentoReport(): array
    {
        $records = $this->repository->findAll();
        
        $report = [
            'totalVeiculos' => 0,
            'faturamentoTotal' => 0.0,
            'detalhePorTipo' => []
        ];

        foreach ($records as $record) {
            $fare = $record->getTotalFare() ?? 0.0; 
            $type = $record->getVehicleType();

            if (!isset($report['detalhePorTipo'][$type])) {
                $report['detalhePorTipo'][$type] = [
                    'count' => 0,
                    'faturamento' => 0.0,
                    'tarifa_hora' => $record->getHourlyRate(),
                ];
            }

            $report['totalVeiculos']++;
            $report['faturamentoTotal'] += $fare;
            $report['detalhePorTipo'][$type]['count']++;
            $report['detalhePorTipo'][$type]['faturamento'] += $fare;
        }

        return $report;
    }
}