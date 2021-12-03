<?php

class Db
{
    private static $instance;
    private $pdo;
    private $log;

    private function __construct()
    {

    }

    private function __clone()
    {

    }
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function connect()
    {
        $host = DB_HOST;
        $dbName = DB_NAME;
        $dbUser = DB_USER;
        $dbPassword = DB_PASSWORD;
        if (!$this->pdo) {
            $this->pdo = new PDO("mysql:host=$host;port=3307;dbname=$dbName", "$dbUser","$dbPassword");
        }
        return $this->pdo;
    }
    public function exec(string $query, $method, array $param = [])
    {
        $this->connect();
        $t = microtime(1);
        $query = $this->pdo->prepare($query);
        $ret = $query->execute($param);
        $t = microtime(1) - $t;
        if (!$ret) {
            if ($query->errorCode()) {
                trigger_error($query->errorInfo());
            }
            return false;
        }

        $this->log[] = [
            'query' => $query,
            't' => $t,
            'method' => $method
        ];
        return $query->rowCount();
    }

    public function lastInsertId()
    {
        $this->connect();
        return $this->pdo->lastInsertId();
    }
    public function getLog()
    {
        $this->connect();
        return json_encode($this->log);
    }

    public function fetchAll(string $query, array $params = [], string $method = '')
    {
        $this->connect();
        $t = microtime(1);
        $query = $this->pdo->prepare($query);
        $ret = $query->execute($param);
        $t = microtime(1) - $t;
        if (!$ret) {
            if ($query->errorCode()) {
                trigger_error(json_encode($query->errorInfo()));
            }
            return false;
        }

        $this->log[] = [
            'query' => $query,
            'method' => $method,
            't' => $t
        ];
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public function fetchOne(string $query, $method = '', array $params = [])
    {
        $t = microtime(true);
        $prepared = $this->connect()->prepare($query);

        $ret = $prepared->execute($params);

        if (!$ret) {
            $errorInfo = $prepared->errorInfo();
            trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: " . $errorInfo[2]);
            return [];
        }

        $data = $prepared->fetchAll(\PDO::FETCH_ASSOC);
        $affectedRows = $prepared->rowCount();


        $this->log[] = [$query, microtime(true) - $t, $method, $affectedRows];
        if (!$data) {
            return false;
        }
        return reset($data);
    }
}