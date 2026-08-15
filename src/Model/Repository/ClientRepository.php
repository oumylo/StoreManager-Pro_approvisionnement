<?php

require_once dirname(__DIR__) . '/Entity/Client.php';

class ClientRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function getClientById(int $id): ?Client
    {
        $sql = "
            SELECT id, nom,prenom,email,tel,limite_credit
            FROM clients
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
            Client::class
        );

        $client = $stmt->fetch();

        if ($client === false) {
            return null;
        }

        return $client;
    }


    public function getAllClients(): array
    {
        $sql = "
            SELECT id, nom, prenom, email, tel, limite_credit
             FROM clients
            ORDER BY nom, prenom ";

        $stmt = $this->pdo->query($sql);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
            Client::class
        );

        return $stmt->fetchAll();
    }

    

    public function saveClient(Client $client): int
    {
        $sql = "
            INSERT INTO clients( nom, prenom, email, tel,limite_credit )
            VALUES( :nom, :prenom, :email, :tel, :limite_credit ) ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nom' => $client->getNom(),
            'prenom' => $client->getPrenom(),
            'email' => $client->getEmail(),
            'tel' => $client->getTel(),
            'limite_credit' => $client->getLimiteCredit()
        ]);

        return (int) $this->pdo->lastInsertId();
    }

}