<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Parking\Infra\Repository\SQLiteParkingRecordRepository;
use Parking\Domain\Service\PricingStrategyFactory;
use Parking\Domain\Service\VehicleTypeValidator;
use Parking\Application\Service\CheckInService;
use Parking\Application\Service\CheckOutService;
use Parking\Application\Service\ReportService;
use Parking\Domain\Constants\VehicleTypeConstant;

$repository = new SQLiteParkingRecordRepository();
$strategyFactory = new PricingStrategyFactory();
$validator = new VehicleTypeValidator();

$checkInService = new CheckInService($repository, $strategyFactory, $validator);
$checkOutService = new CheckOutService($repository);
$reportService = new ReportService($repository);

$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    $plate = strtoupper(trim($_POST['plate'] ?? ''));

    try {
        if ($_POST['action'] === 'check_in') {
            $vehicleType = strtolower(trim($_POST['vehicle_type'] ?? ''));
            
            $input = [
                'plate' => $plate,
                'vehicle_type' => $vehicleType,
                'time' => (new DateTimeImmutable())->format(DateTimeImmutable::ATOM) 
            ];
            
            $record = $checkInService->execute($input);
            $message = "✅ Check-In de **{$plate}** (Tarifa: R$ " . number_format($record->getHourlyRate(), 2, ',', '.') . "/h) registrado com sucesso!";

        } elseif ($_POST['action'] === 'check_out') {
            
            $timeOut = new DateTimeImmutable();
            $record = $checkOutService->execute($plate, $timeOut);
            
            $message = "✅ Check-Out de **{$plate}** realizado! Total a Pagar: **R$ " . number_format($record->getTotalFare(), 2, ',', '.') . "**";
        }
    } catch (\InvalidArgumentException $e) {
        $error = "🚨 ERRO DE VALIDAÇÃO: " . $e->getMessage();
    } catch (\RuntimeException $e) {
        $error = "🚨 ERRO DE EXECUÇÃO: " . $e->getMessage();
    } catch (\Exception $e) {
        $error = "🚨 ERRO INESPERADO: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estacionamento SOLID</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f9; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h1, h2 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        form { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; background: #fafafa; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], select { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; margin-right: 10px; }
        .btn-in { background-color: #28a745; }
        .btn-out { background-color: #dc3545; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .report-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .report-table th, .report-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .report-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>

<div class="container">
    <h1>🅿️ Sistema de Estacionamento SOLID</h1>

    <?php if ($message): ?>
        <div class="message success"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <h2>Registro de Entrada/Saída</h2>
        <input type="hidden" id="action" name="action" value="check_in">
        
        <label for="plate">Placa do Veículo:</label>
        <input type="text" id="plate" name="plate" placeholder="Ex: ABC-1234" required maxlength="10">

        <label for="vehicle_type">Categoria:</label>
        <select id="vehicle_type" name="vehicle_type" required>
            <?php foreach (VehicleTypeConstant::getTypes() as $type): ?>
                <option value="<?php echo $type; ?>"><?php echo ucfirst($type); ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-in" onclick="document.getElementById('action').value='check_in';">
            Registrar Entrada (Check-In)
        </button>
        <button type="submit" class="btn-out" onclick="document.getElementById('action').value='check_out';">
            Registrar Saída (Check-Out)
        </button>
    </form>

    <hr>
    
    <h2>📊 Relatório de Faturamento</h2>
    <?php
    try {
        $report = $reportService->generateFaturamentoReport();
        echo "<p>Total de Veículos Registrados (Entradas): <strong>{$report['totalVeiculos']}</strong></p>";
        echo "<p>Faturamento Total (Encerrados): <strong>R$ " . number_format($report['faturamentoTotal'], 2, ',', '.') . "</strong></p>";
    ?>

    <table class="report-table">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Contagem</th>
                <th>Tarifa/Hora</th>
                <th>Faturamento (Encerrado)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($report['detalhePorTipo'] as $type => $detail): ?>
            <tr>
                <td><?php echo ucfirst($type); ?></td>
                <td><?php echo $detail['count']; ?></td>
                <td>R$ <?php echo number_format($detail['tarifa_hora'], 2, ',', '.'); ?></td>
                <td>R$ <?php echo number_format($detail['faturamento'], 2, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php 
    } catch (\Exception $e) {
        echo "<p class='error'>Erro ao gerar relatório: " . $e->getMessage() . "</p>";
    }
    ?>
</div>

</body>
</html>