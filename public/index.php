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

$message = $_GET['message'] ?? null;
$error = $_GET['error'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    $plate = strtoupper(trim($_POST['plate'] ?? ''));
    $redirect_params = [];
    
    try {
        if ($_POST['action'] === 'check_in') {
            $vehicleType = strtolower(trim($_POST['vehicle_type'] ?? ''));
            
            $input = [
                'plate' => $plate,
                'vehicle_type' => $vehicleType,
                'time' => (new DateTimeImmutable())->format(DateTimeImmutable::ATOM) 
            ];
            
            $record = $checkInService->execute($input);
            $msg = "Check-In de **{$plate}** (Tarifa: R\$ " . number_format($record->getHourlyRate(), 2, ',', '.') . "/h) registrado com sucesso!";
            $redirect_params['message'] = urlencode($msg);

        } elseif ($_POST['action'] === 'check_out') {
            
            $vehicleType = strtolower(trim($_POST['vehicle_type'] ?? ''));
            
            $timeOut = new DateTimeImmutable();

            $record = $checkOutService->execute($plate, $vehicleType, $timeOut);
            
            $fareFormatted = number_format($record->getTotalFare(), 2, ',', '.');
            $msg = "Check-Out de **{$plate}** realizado! Valor Pago: **R\$ {$fareFormatted}**";
            $redirect_params['message'] = urlencode($msg);
        }

    } catch (\InvalidArgumentException $e) {
        $err = "ERRO DE VALIDAÇÃO: " . $e->getMessage();
        $redirect_params['error'] = urlencode($err);
    } catch (\RuntimeException $e) {
        $err = "ERRO DE EXECUÇÃO: " . $e->getMessage();
        $redirect_params['error'] = urlencode($err);
    } catch (\Exception $e) {
        $err = "ERRO INESPERADO: " . $e->getMessage();
        $redirect_params['error'] = urlencode($err);
    }
    
    $query = http_build_query($redirect_params);
    header("Location: index.php?" . $query);
    exit;
}

try {
    $allRecords = $repository->findAll(); 
} catch (\Exception $e) {
    $error = "Erro ao buscar registros: " . $e->getMessage();
    $allRecords = [];
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
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
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
    <h1>Sistema de Estacionamento Inteligente SOLID</h1>

    <?php if ($message): ?>
        <div class="message success"><?php echo urldecode($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="message error"><?php echo urldecode($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <h2>Registro de Entrada/Saída</h2>
        <input type="hidden" id="action" name="action" value="check_in">
        
        <label for="plate">Placa do Veículo:</label>
        <input type="text" id="plate" name="plate" placeholder="Ex: ABC-1234" required maxlength="8">

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
    
    <h2>Histórico de Registros</h2>
    <table class="report-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Placa</th>
                <th>Tipo</th>
                <th>Entrada</th>
                <th>Saída</th>
                <th>Taxa/Hora</th>
                <th>Valor Final</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($allRecords)): ?>
                <?php foreach ($allRecords as $record): 
                    $entryTime = $record->getTimeIn()->format('d/m H:i:s');
                    $exitTime = $record->getTimeOut() ? $record->getTimeOut()->format('d/m H:i:s') : '<span style="color: green; font-weight: bold;">Em Aberto</span>';
                    $finalFare = $record->getTotalFare() ? number_format($record->getTotalFare(), 2, ',', '.') : '-';
                ?>
                <tr>
                    <td><?php echo $record->getId(); ?></td>
                    <td><?php echo $record->getPlate(); ?></td>
                    <td><?php echo ucfirst($record->getVehicleType()); ?></td>
                    <td><?php echo $entryTime; ?></td>
                    <td><?php echo $exitTime; ?></td>
                    <td>R$ <?php echo number_format($record->getHourlyRate(), 2, ',', '.'); ?></td>
                    <td>R$ <?php echo $finalFare; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Nenhum registro encontrado no banco de dados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>