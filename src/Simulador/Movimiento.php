<?php

namespace App\Simulador;

class Movimiento
{
    private Ascensor $ascensor;
    private int $destino;
    private bool $finalizado = false;
    private ?Movimiento $siguiente = null;

    public function __construct(Ascensor $ascensor, int $destino, ?Movimiento $siguiente = null)
    {
        $this->ascensor = $ascensor;
        $this->destino = $destino;
        $this->siguiente = $siguiente;
    }

    public function avanzar()
    {
        if($this->ascensor->getPosicion() < $this->destino) {
            $this->ascensor->setPosicion($this->ascensor->getPosicion() + 1);
        }

        if($this->ascensor->getPosicion() > $this->destino) {
            $this->ascensor->setPosicion($this->ascensor->getPosicion() - 1);
        }

        if ($this->ascensor->getPosicion() === $this->destino) {
            $this->finalizado = true;
        }
    }

    /**
     * @return Ascensor
     */
    public function getAscensor(): Ascensor
    {
        return $this->ascensor;
    }

    /**
     * @return bool
     */
    public function isFinalizado(): bool
    {
        return $this->finalizado;
    }

    /**
     * @return Movimiento|null
     */
    public function getSiguiente(): ?Movimiento
    {
        return $this->siguiente;
    }

    public function getPosicion(): int
    {
        return $this->getAscensor()->getPosicion();
    }
}