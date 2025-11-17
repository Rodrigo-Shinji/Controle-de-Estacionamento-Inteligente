<?php
declare(strict_types=1);

namespace Parking\Infra\Database;

use \PDO;

class SQLiteConnection
{
    private const DB_FILE = __DIR__ . '/../../Domain/storage/database.sqlite';
    
    public static function connect(): PDO
    {
        try {

            $pdo = new PDO('sqlite:' . self::DB_FILE);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return $pdo;
        } catch (\PDOException $e) {
            throw new \RuntimeException("Erro ao conectar ao banco de dados SQLite: " . $e->getMessage());
        }
    }
}