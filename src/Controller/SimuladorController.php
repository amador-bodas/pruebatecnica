<?php

namespace App\Controller;

use App\Simulador\SimuladorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SimuladorController extends AbstractController
{
    private SimuladorService $simulador;

    public function __construct(SimuladorService $simulador)
    {
        $this->simulador = $simulador;
    }

    public function execute()
    {
        $this->simulador->secuenciar();
    }
}