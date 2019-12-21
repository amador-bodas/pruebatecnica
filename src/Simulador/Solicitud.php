<?php

namespace App\Simulador;

final class Solicitud
{
    private int $destino;
    private int $origen;

    public function __construct(int $destino, int $origen)
    {
        $this->destino = $destino;
        $this->origen = $origen;
    }

    public function getDestino(): int
    {
        return $this->destino;
    }

    public function getOrigen(): int
    {
        return $this->origen;
    }
}