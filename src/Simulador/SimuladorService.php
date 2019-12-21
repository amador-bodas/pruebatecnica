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
    /**
     * @var Movimiento[]
     */
    private array $colaMovimientos = [];
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
            foreach ($this->secuencias as $secuencia) {
                if($secuencia->instanteEstaEnIntervalo($instante) && ($minuto % $secuencia->getPeriodo()) === 0) {
                    $this->solicitarAscensor($secuencia);
                }
            }
            $this->procesarCola();
            $this->procesarMovimiento();
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
{        /** @var Ascensor[] $ascensoresDisponibles */
        $ascensoresDisponibles = array_filter($this->ascensores, fn(Ascensor $ascensor) => $ascensor->isDisponible());
        while(count($this->colaLlamadas) > 0 && count($ascensoresDisponibles) > 0) {
            $solicitud = array_shift($this->colaLlamadas);
            $ascensorDisponible = array_shift($ascensoresDisponibles);
            $ascensorDisponible->setDisponible(false);
            $this->colaMovimientos[] = new Movimiento(
                $ascensorDisponible,
                $solicitud->getOrigen(),
                new Movimiento($ascensorDisponible, $solicitud->getDestino())
            );
        }
    }

    private function logPosicion(\DateTime $instante): void
    {
        foreach ($this->ascensores as $ascensor) {
            $this->logPosiciones[$instante->format("H:i")][$ascensor->getName()] = $ascensor->getPosicion();
        }
    }

    private function procesarMovimiento()
    {
        $colaTemporal = [];
        while(count ($this->colaMovimientos) > 0) {
            $movimiento = array_shift($this->colaMovimientos);
            $movimiento->avanzar();
            if($movimiento->isFinalizado()) {
                if($movimiento->getSiguiente()) {
                    $colaTemporal[] = $movimiento->getSiguiente();
                }
                //dump($movimiento);
                $keyAscensor = $this->findKeyAscensorByName($movimiento->getAscensor()->getName());
                $this->ascensores[$keyAscensor]->setPosicion($movimiento->getPosicion());
                if(!$movimiento->getSiguiente()) {
                    $this->ascensores[$keyAscensor]->setDisponible(true);
                }
            }else{
                $colaTemporal[] = $movimiento;
            }
        }
        
        foreach ($colaTemporal as $movimientoTemporal) {
            $this->colaMovimientos[] = $movimientoTemporal;
        }
    }

    private function findKeyAscensorByName(string $name): ?int
    {

        foreach ($this->ascensores as $key => $ascensor) {
            if($ascensor->getName() === $name) {
                return $key;
            }
        }

        return null;
    }
}