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
        $logs = $this->simulador->secuenciar();

        return $this->render('simulador/simulador.html.twig', ['logs' => $logs]);
    }
}