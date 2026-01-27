<?php
// Database.php（リモートMySQL対応版）

class Database {
    private $host = '172.22.89.240'; // MySQL サーバーの IP
    private $port = 3307;            // ポート番号
    private $db   = 'libraty';       // 実際の DB 名
    private $user = 'root';
    private $pass = 'oc@pASS2024';
    private $charset = 'utf8mb4';
    private $pdo;
    private static $instance = null;

    private function __construct() {
        $dsn = "mysql:host=$this->host;port=$this->port;dbname=$this->db;charset=$this->charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            echo "DB接続失敗: " . $e->getMessage();
            exit;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
