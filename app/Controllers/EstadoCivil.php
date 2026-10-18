<?php

namespace App\Controllers;

use App\Models\EstadoCivilModel;

class EstadoCivil extends BaseController
{
    protected EstadoCivilModel $estadoCivilModel;

    public function __construct()
    {
        $this->estadoCivilModel = new EstadoCivilModel();
    }

    public function index($id = null)
    {
        return $this->actual($id);
    }

    public function actual($id = null)
    {
        if ($id !== null) {
            $estadoCivil = $this->estadoCivilModel->find((int)$id);
        } else {
            $estadoCivil = $this->estadoCivilModel->elultimo();
        }

        $currentId = $estadoCivil ? (int)$estadoCivil['idestadocivil'] : null;
        $posicion  = $currentId ? $this->estadoCivilModel->obtenerPosicion($currentId) : ['pos' => 0, 'total' => 0];

        $data = [
            'title'       => $estadoCivil ? 'Estado Civil: ' . esc($estadoCivil['nombre']) : 'Ficha de Estado Civil',
            'module'      => 'estadocivil',
            'estadoCivil' => $estadoCivil,
            'currentId'   => $currentId,
            'posicion'    => $posicion,
        ];

        return view('estadocivil/estadocivil_record', $data);
    }

    public function elprimero()
    {
        $first = $this->estadoCivilModel->elprimero();
        return $this->actual($first ? $first['idestadocivil'] : null);
    }

    public function elultimo()
    {
        $last = $this->estadoCivilModel->elultimo();
        return $this->actual($last ? $last['idestadocivil'] : null);
    }

    public function siguiente($id)
    {
        $next = $this->estadoCivilModel->siguiente((int)$id);
        return $this->actual($next ? $next['idestadocivil'] : $id);
    }

    public function anterior($id)
    {
        $prev = $this->estadoCivilModel->anterior((int)$id);
        return $this->actual($prev ? $prev['idestadocivil'] : $id);
    }

    public function listar()
    {
        $search = $this->request->getGet('q');

        $estadosCiviles = $this->estadoCivilModel->getEstadosCivilesConConteo($search);

        $data = [
            'title'          => 'Catálogo de Estados Civiles',
            'module'         => 'estadocivil',
            'estadosCiviles' => $estadosCiviles,
            'search'         => $search,
            'total'          => count($estadosCiviles),
        ];

        return view('estadocivil/estadocivil_list', $data);
    }

    public function add()
    {
        $data = [
            'title'  => 'Registrar Nuevo Estado Civil',
            'module' => 'estadocivil',
            'errors' => session()->getFlashdata('errors') ?? [],
            'old'    => session()->getFlashdata('old') ?? [],
        ];

        return view('estadocivil/estadocivil_form', $data);
    }

    public function store()
    {
        $postData = [
            'nombre' => trim($this->request->getPost('nombre') ?? ''),
        ];

        if (!$this->estadoCivilModel->insert($postData)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->estadoCivilModel->errors())
                             ->with('old', $postData);
        }

        $nuevoId = $this->estadoCivilModel->getInsertID();

        return redirect()->to(base_url('estadocivil/actual/' . $nuevoId))
                         ->with('success', '¡Registro de Estado Civil creado exitosamente!');
    }

    public function edit($id)
    {
        $estadoCivil = $this->estadoCivilModel->find((int)$id);
        if (!$estadoCivil) {
            return redirect()->to(base_url('estadocivil'))->with('error', 'Registro no encontrado.');
        }

        $data = [
            'title'       => 'Editar Estado Civil: ' . esc($estadoCivil['nombre']),
            'module'      => 'estadocivil',
            'estadoCivil' => $estadoCivil,
            'currentId'   => (int)$id,
            'errors'      => session()->getFlashdata('errors') ?? [],
        ];

        return view('estadocivil/estadocivil_edit', $data);
    }

    public function update($id)
    {
        $estadoCivil = $this->estadoCivilModel->find((int)$id);
        if (!$estadoCivil) {
            return redirect()->to(base_url('estadocivil'))->with('error', 'Registro no encontrado.');
        }

        $postData = [
            'nombre' => trim($this->request->getPost('nombre') ?? ''),
        ];

        if (!$this->estadoCivilModel->update((int)$id, $postData)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->estadoCivilModel->errors());
        }

        return redirect()->to(base_url('estadocivil/actual/' . $id))
                         ->with('success', '¡Registro de Estado Civil actualizado correctamente!');
    }

    public function delete($id)
    {
        $estadoCivil = $this->estadoCivilModel->find((int)$id);
        if (!$estadoCivil) {
            return redirect()->to(base_url('estadocivil'))->with('error', 'Registro no encontrado.');
        }
        if ($this->estadoCivilModel->contarPersonas((int)$id) > 0) {
            return redirect()->to(base_url('estadocivil/actual/' . $id))
                             ->with('error', 'No se puede eliminar: hay personas con este estado civil.');
        }
        
        $this->estadoCivilModel->delete((int)$id);

        $ultimo = $this->estadoCivilModel->elultimo();
        $redirectUrl = $ultimo ? base_url('estadocivil/actual/' . $ultimo['idestadocivil']) : base_url('estadocivil');

        return redirect()->to($redirectUrl)
                         ->with('success', 'Estado Civil #' . $id . ' eliminado exitosamente.');
    }
}
