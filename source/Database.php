<?php
class Database {

    private ?PDO $conn = null;

    public function __construct(
        private string $host,
        private string $name,
        private string $user,
        private string $psw) {

    }

    public function getConnection() : PDO {
        if($this->conn === null) {

        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";
        $this->conn = new PDO($dsn, $this->user, $this->psw, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,//those two attr specify not to convert stmt from base to string
            PDO::ATTR_STRINGIFY_FETCHES => false//numeric values stay numeric
        ]);
    } 

    return $this->conn;

    }
}