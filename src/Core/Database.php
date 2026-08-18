<?php

class Database
{
    private static ?Database $instance = null;

    private PDO $connection;

    private function __construct()
    {
        try {
            $this->connection = new PDO(
                "pgsql:host=localhost;dbname=storemanager;port=5432",
                "postgres",
                "boubouni",
                [
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
        } catch (PDOException $e) {

            $this->connection = new PDO(
                "sqlite:" . dirname(dirname(__DIR__)) . "/Database/erp.db",
                null,
                null,
                [
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
        }
    }

    
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

   
    public static function getConnection(): PDO
    {
        return self::getInstance()->connection;
    }

   
    public static function query(
        string $sql,
        bool $single = true
    ): array {

        $query = self::getConnection()->query($sql);

        return $single
            ? $query->fetch()
            : $query->fetchAll();
    }

    
    public static function prepare(
        string $sql,
        array $datas = []
    ): PDOStatement {

        $prepare = self::getConnection()->prepare($sql);

        $prepare->execute($datas);

        return $prepare;
    }

    public static function executeQuery(
        string $sql,
        array $datas = [],
        bool $single = true
    ): array {

        $statement = self::prepare($sql, $datas);

        return $single
            ? $statement->fetch()
            : $statement->fetchAll();
    }

    
    public static function executeUpdate(
        string $sql,
        array $datas = []
    ): int {

        $prepare = self::prepare($sql, $datas);

        if (str_starts_with(strtoupper(trim($sql)), 'INSERT')) {
            return (int) self::getConnection()->lastInsertId();
        }

        return $prepare->rowCount();
    }
}