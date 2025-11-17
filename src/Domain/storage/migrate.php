<?php
declare(strict_types=1);

$dir = __DIR__;
$dbPath = $dir . '/storage/database.sqlite';

if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

try{
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = <<<SQL
CREATE TABLE IF NOT EXISTS passages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  plate TEXT NOT NULL,
  vehicle_type TEXT NOT NULL,
  time_in DATETIME NOT NULL,
  time_out DATETIME NULL,
  hourly_rate REAL NOT NULL,
  total_fare REAL NULL
);
SQL;

$pdo->exec($sql);

echo "OK: database.sqlite e tabela passages criados/atualizados.\n";
} catch (\PDOException $e) {
    echo "Erro ao executar a migração: " . $e->getMessage() . "\n";
}