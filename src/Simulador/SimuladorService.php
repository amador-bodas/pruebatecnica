<?php

namespace App\Simulador;

class SimuladorService
{
    private const NUMERO_ASCENSORES = 3;
    private const PLANTAS = [0,1,2,3];

    /**
     * @var Ascensor[]
     */
    private array $ascensores;
    /**
     * @var Secuencia[]
     */
    private array $secuencias;
    /**
     * @var Solicitud[]
     */
    private array $colaLlamadas = [];
    private \DateTime $inicio;
    private \DateTime $fin;
    private array $logPosiciones = [];


    public function __construct()
    {
        $this->inicializarAscensores();
        $this->inicializarSecuencias();
        $this->inicializarRango();
    }


    public function secuenciar(): array
    {
        $intervalo = new \DateInterval('P0YT1M'); //1 minuto
        $periodo = new \DatePeriod($this->inicio, $intervalo, $this->fin);
        /** @var \DateTime $instante */
        foreach ($periodo as $instante) {
            $minuto = (int) $instante->format('i');
            $this->resetDisponibilidad();
            foreach ($this->secuencias as $secuencia) {
                if($secuencia->instanteEstaEnIntervalo($instante) && ($minuto % $secuencia->getPeriodo()) === 0) {
                    $this->solicitarAscensor($secuencia);
                }
            }
            $this->procesarCola();
            $this->logPosicion($instante);
        }

        return $this->logPosiciones;
    }

    private function inicializarAscensores(): void
    {
        for ($i = 0; $i < self::NUMERO_ASCENSORES; $i++) {
            $this->ascensores[] = new Ascensor("ascensor".$i);
        }
    }

    private function inicializarSecuencias(): void
    {
        $secuencia = Secuencia::fromStartAndEnd(9, 0, 11, 0, 5);
        $secuencia->addSolicitud(new Solicitud(2, 0));
        $this->secuencias[] = $secuencia;
        $secuencia = Secuencia::fromStartAndEnd(9, 0, 10, 0, 10);
        $secuencia->addSolicitud(new Solicitud(1,0));
        $this->secuencias[] = $secuencia;
        $secuencia = Secuencia::fromStartAndEnd(11,0,18,20,20);
        $secuencia->addSolicitud(new Solicitud(1,0));
        $secuencia->addSolicitud(new Solicitud(2,0));
        $secuencia->addSolicitud(new Solicitud(3,0));
        $this->secuencias[] = $secuencia;
        $secuencia = Secuencia::fromStartAndEnd(14,0,15,0,4);
        $secuencia->addSolicitud(new Solicitud(0,1));
        $secuencia->addSolicitud(new Solicitud(0,2));
        $secuencia->addSolicitud(new Solicitud(0,3));
        $this->secuencias[] = $secuencia;
    }

    private function inicializarRango(): void
    {
        $this->inicio = new \DateTime();
        $this->inicio->setTime(9, 0);
        $this->fin = new \DateTime();
        $this->fin->setTime(20, 1);
    }

    private function solicitarAscensor(Secuencia $secuencia): void
    {
        foreach ($secuencia->getSolicitudes() as $solicitud) {
            $this->colaLlamadas[] = $solicitud;
        }
    }

    private function procesarCola()
    {
        /** @var Ascensor[] $ascensoresDisponibles */
        $ascensoresDisponibles = array_filter($this->ascensores, fn(Ascensor $ascensor) => $ascensor->isDisponible());
        foreach ($ascensoresDisponibles as $ascensorDisponible) {
            if(count($this->colaLlamadas) > 0) {
                $solicitud = array_shift($this->colaLlamadas);
                $ascensorDisponible->setDisponible(false);
                $ascensorDisponible->setPosicion($solicitud->getDestino());
            }
        }
    }

    private function resetDisponibilidad()
    {
        foreach ($this->ascensores as $ascensor) {
            $ascensor->setDisponible(true);
        }
    }

    private function logPosicion(\DateTime $instante): void
    {
        foreach ($this->ascensores as $ascensor) {
            $this->logPosiciones[$instante->format("H:i")][$ascensor->getName()] = $ascensor->getPosicion();
        }
    }
}