<?php

namespace App\Simulador;

final class Secuencia
{
    private \DateTime $inicio;
    private \DateTime $fin;
    private int $periodo;
    /**
     * @var Solicitud[]
     */
    private array $solicitudes = [];

    public function __construct(\DateTime $inicio, \DateTime $fin, int $periodo)
    {
        $this->inicio = $inicio;
        $this->fin = $fin;
        $this->periodo = $periodo;
    }

    public static function fromStartAndEnd(
        int $hourStart,
        int $minuteStart,
        int $hourEnd,
        int $minuteEnd,
        int $periodo
    )
    {
        $inicioSecuencia = new \DateTime();
        $inicioSecuencia->setTime($hourStart,$minuteStart);
        $finSecuencia = new \DateTime();
        $finSecuencia->setTime($hourEnd, $minuteEnd);

        return new static(
            $inicioSecuencia,
            $finSecuencia,
            $periodo
        );
    }

    public function instanteEstaEnIntervalo(\DateTime $instante): bool
    {
        return (
            $this->inicio->getTimestamp() <= $instante->getTimestamp() && $this->fin->getTimestamp() >= $instante->getTimestamp()
        );
    }

    public function getPeriodo(): int
    {
        return $this->periodo;
    }

    public function addSolicitud(Solicitud $solicitud)
    {
        $this->solicitudes[] = $solicitud;
    }

    /**
     * @return Solicitud[]
     */
    public function getSolicitudes(): array
    {
        return $this->solicitudes;
    }
}