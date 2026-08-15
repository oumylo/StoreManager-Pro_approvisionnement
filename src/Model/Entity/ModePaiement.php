<?php

class ModePaiement
{
    private ?int $id;
    private string $mode;

    public function __construct(?int $id, string $mode)
    {
        $this->id = $id;
        $this->setMode($mode);
    }

    // GETTER

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    // SETTER

    public function setMode(string $mode): void
    {
        $mode = strtoupper(trim($mode));

        if ($mode === '') {
            throw new InvalidArgumentException(
                "Le mode de paiement est obligatoire."
            );
        }

        $this->mode = $mode;
    }
}