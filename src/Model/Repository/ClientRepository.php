<?php

require_once dirname(__DIR__) . '/Entity/Client.php';
require_once dirname(dirname(__DIR__)) . '/Core/Database.php';

class ClientRepository
{
    public static function getAllClients(): array
    {
        $sql = "SELECT * FROM clients ORDER BY nom";

        return Database::query($sql, false, Client::class);
    }



    public static function createClient(Client $client): int
    {
        $sql = "INSERT INTO clients
                (nom, prenom, email, tel, limite_credit)
                VALUES
                (:nom, :prenom, :email, :tel, :limite_credit)";

        return Database::executeUpdate($sql, [
            'nom' => $client->getNom(),
            'prenom' => $client->getPrenom(),
            'email' => $client->getEmail(),
            'tel' => $client->getTel(),
            'limite_credit' => $client->getLimiteCredit()
        ]);
    }
}