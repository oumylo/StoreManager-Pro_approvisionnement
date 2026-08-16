<?php

require_once dirname(__DIR__) . '/Entity/Client.php';
require_once dirname(__DIR__) . '/src/Core/Database.php';



class ClientRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = connexionDB();
    }


    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM clients ORDER BY nom");
        $stmt->setFetchMode(PDO::FETCH_CLASS, Client::class);

        return $stmt->fetchAll();
    }

 
    public function findById(int $id): ?Client
    {
        $stmt = $this->pdo->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Client::class);

        $client = $stmt->fetch();

        return $client !== false ? $client : null;
    }

    
    public function findByEmail(string $email): ?Client
    {
        $stmt = $this->pdo->prepare("SELECT * FROM clients WHERE email = ?");
        $stmt->execute([$email]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Client::class);

        $client = $stmt->fetch();

        return $client !== false ? $client : null;
    }

   
    public function create(Client $client): int
    {
        $sql = "INSERT INTO clients (nom, prenom, email, tel, limite_credit)
                VALUES (:nom, :prenom, :email, :tel, :limite_credit)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nom'           => $client->getNom(),
            'prenom'        => $client->getPrenom(),
            'email'         => $client->getEmail(),
            'tel'           => $client->getTel(),
            'limite_credit' => $client->getLimiteCredit(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

   
    public function update(Client $client): bool
    {
        $sql = "UPDATE clients
                SET nom = :nom,
                    prenom = :prenom,
                    email = :email,
                    tel = :tel,
                    limite_credit = :limite_credit
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'nom'           => $client->getNom(),
            'prenom'        => $client->getPrenom(),
            'email'         => $client->getEmail(),
            'tel'           => $client->getTel(),
            'limite_credit' => $client->getLimiteCredit(),
            'id'            => $client->getId(),
        ]);
    }


    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM clients WHERE id = ?");

        return $stmt->execute([$id]);
    }

    
    public function getCreditUtilise(int $clientId): float
    {
        $sql = "SELECT COALESCE(SUM(reste_du), 0) AS total
                FROM dettes
                WHERE client_id = ? AND statut = 'NON_SOLDEE'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clientId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (float) $result['total'];
    }
}