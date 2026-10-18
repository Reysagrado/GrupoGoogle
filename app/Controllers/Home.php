<?php

namespace App\Controllers;

use App\Models\PersonaModel;
use App\Models\SexoModel;
use App\Models\EstadoCivilModel;
use App\Models\DireccionModel;
use App\Models\CorreoElectronicoModel;

class Home extends BaseController
{
    protected PersonaModel $personaModel;
    protected SexoModel $sexoModel;
    protected EstadoCivilModel $estadoCivilModel;
    protected DireccionModel $direccionModel;
    protected CorreoElectronicoModel $correoModel;

    public function __construct()
    {
        $this->personaModel     = new PersonaModel();
        $this->sexoModel        = new SexoModel();
        $this->estadoCivilModel = new EstadoCivilModel();
        $this->direccionModel   = new DireccionModel();
        $this->correoModel      = new CorreoElectronicoModel();
    }

    public function index()
    {
        $stats = $this->personaModel->getDashboardStats();

        $data = [
            'title'  => 'Panel Principal - Sistema de Información eysfwis',
            'module' => 'home',
            'stats'  => $stats,
     'cumpleanos' => $this->personaModel->getCumpleanosDelMes(),
        ];

        return view('home/index', $data);
    }
}
