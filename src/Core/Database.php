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
     public function getConnection(): PDO
    {
        return $this->connection;
    }
}

function connexionDB(): PDO
{
    return Database::getInstance()->getConnection();
}

function query(PDO $pdo, string $sql, bool $single = true): array
{
    $query = $pdo->query($sql);

    return $single
        ? $query->fetch()
        : $query->fetchAll();
}


function prepare(PDO $pdo, string $sql, array $datas)
{
    $prepare = $pdo->prepare($sql);
    $prepare->execute($datas);

    return $prepare;
}


function executeQuery(
    PDO $pdo,
    string $sql,
    array $datas,
    bool $single = true
): array {

    $statement = prepare($pdo, $sql, $datas);

    return $single
        ? $statement->fetch()
        : $statement->fetchAll();
}


function executeUpdate(
    PDO $pdo,
    string $sql,
    array $datas
): int {

    $prepare = prepare($pdo, $sql, $datas);

    return str_starts_with(strtoupper(trim($sql)), 'INSERT')
        ? (int) $pdo->lastInsertId()
        : $prepare->rowCount();
}