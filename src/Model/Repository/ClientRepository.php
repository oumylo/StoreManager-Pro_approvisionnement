
<?php

require_once dirname(__DIR__) . '/Entity/Client.php';
require_once dirname(dirname(__DIR__)) . '/Core/Database.php';

class ClientRepository
{
    private PDO $pdo;


    public function __construct()
    {
        $this->pdo = connexionDB();
    }


   
    public function findAll(): array
    {
        $sql = "SELECT * FROM clients ORDER BY nom";

        $stmt = $this->pdo->query($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, Client::class);

        $clients = $stmt->fetchAll();

        return $clients;
    }


    public function findById(int $id): ?Client
    {
        $sql = "SELECT * FROM clients WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([$id]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, Client::class);

        $client = $stmt->fetch();

        if ($client === false) {
            return null;
        }

        return $client;
    }


    
    public function findByEmail(string $email): ?Client
    {
        $sql = "SELECT * FROM clients WHERE email = ?";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([$email]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, Client::class);

        $client = $stmt->fetch();

        if ($client === false) {
            return null;
        }

        return $client;
    }


   
    public function create(Client $client): int
    {
        $sql = "INSERT INTO clients
                (nom, prenom, email, tel, limite_credit)
                VALUES
                (:nom, :prenom, :email, :tel, :limite_credit)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nom' => $client->getNom(),
            'prenom' => $client->getPrenom(),
            'email' => $client->getEmail(),
            'tel' => $client->getTel(),
            'limite_credit' => $client->getLimiteCredit()
        ]);

        $id = $this->pdo->lastInsertId();

        return (int) $id;
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

        $resultat = $stmt->execute([
            'nom' => $client->getNom(),
            'prenom' => $client->getPrenom(),
            'email' => $client->getEmail(),
            'tel' => $client->getTel(),
            'limite_credit' => $client->getLimiteCredit(),
            'id' => $client->getId()
        ]);

        return $resultat;
    }


    
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM clients WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);

        $resultat = $stmt->execute([$id]);

        return $resultat;
    }


   
    public function getCreditUtilise(int $clientId): float
    {
        $sql = "SELECT COALESCE(SUM(reste_du), 0) AS total
                FROM dettes
                WHERE client_id = ?
                AND statut = 'NON_SOLDEE'";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([$clientId]);

        $resultat = $stmt->fetch(PDO::FETCH_ASSOC);

        $creditUtilise = $resultat['total'];

        return (float) $creditUtilise;
    }
}

