<?php

namespace App\Simulador;

final class Ascensor
{
    private int $posicion = 0;
    private bool $disponible = true;
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getPosicion(): int
    {
        return $this->posicion;
    }

    public function setPosicion(int $posicion): void
    {
        $this->posicion = $posicion;
    }

    public function isDisponible(): bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): void
    {
        $this->disponible = $disponible;
    }

    public function getName(): string
    {
        return $this->name;
    }
}