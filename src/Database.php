<?php
namespace App;
use PDO;
use PDOException;

class Database {
    public static function getConnection() {
        $dsn  = 'mysql:host=127.0.0.1;dbname=sensors_db;charset=utf8mb4';
        $user = 'root';
        $pass = '';

        try {
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'База недоступна'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }
    }
}