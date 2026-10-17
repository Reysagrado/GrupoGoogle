<?php

namespace App\Controllers;

use App\Models\SexoModel;

class Sexo extends BaseController
{
    protected SexoModel $sexoModel;

    public function __construct()
    {
        $this->sexoModel = new SexoModel();
    }

    public function index($id = null)
    {
        return $this->actual($id);
    }

    public function actual($id = null)
    {
        if ($id !== null) {
            $sexo = $this->sexoModel->find((int)$id);
        } else {
            $sexo = $this->sexoModel->elultimo();
        }

        $currentId = $sexo ? (int)$sexo['idsexo'] : null;
        $posicion  = $currentId ? $this->sexoModel->obtenerPosicion($currentId) : ['pos' => 0, 'total' => 0];

        $data = [
            'title'     => $sexo ? 'Sexo: ' . esc($sexo['nombre']) : 'Ficha de Sexo',
            'module'    => 'sexo',
            'sexo'      => $sexo,
            'currentId' => $currentId,
            'posicion'  => $posicion,
        ];

        return view('sexo/sexo_record', $data);
    }

    public function elprimero()
    {
        $first = $this->sexoModel->elprimero();
        return $this->actual($first ? $first['idsexo'] : null);
    }

    public function elultimo()
    {
        $last = $this->sexoModel->elultimo();
        return $this->actual($last ? $last['idsexo'] : null);
    }

    public function siguiente($id)
    {
        $next = $this->sexoModel->siguiente((int)$id);
        return $this->actual($next ? $next['idsexo'] : $id);
    }

    public function anterior($id)
    {
        $prev = $this->sexoModel->anterior((int)$id);
        return $this->actual($prev ? $prev['idsexo'] : $id);
    }

    public function listar()
    {
        $search = $this->request->getGet('q');
            
        $sexos = $this->sexoModel->getSexosConConteo($search);

        $data = [
            'title'  => 'Catálogo de Sexos',
            'module' => 'sexo',
            'sexos'  => $sexos,
            'search' => $search,
            'total'  => count($sexos),
        ];

        return view('sexo/sexo_list', $data);
    }

    public function add()
    {
        $data = [
            'title'  => 'Registrar Nuevo Sexo',
            'module' => 'sexo',
            'errors' => session()->getFlashdata('errors') ?? [],
            'old'    => session()->getFlashdata('old') ?? [],
        ];

        return view('sexo/sexo_form', $data);
    }

    public function store()
    {
        $postData = [
            'nombre' => trim($this->request->getPost('nombre') ?? ''),
        ];

        if (!$this->sexoModel->insert($postData)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->sexoModel->errors())
                             ->with('old', $postData);
        }

        $nuevoId = $this->sexoModel->getInsertID();

        return redirect()->to(base_url('sexo/actual/' . $nuevoId))
                         ->with('success', '¡Registro de Sexo creado exitosamente!');
    }

    public function edit($id)
    {
        $sexo = $this->sexoModel->find((int)$id);
        if (!$sexo) {
            return redirect()->to(base_url('sexo'))->with('error', 'Registro no encontrado.');
        }

        $data = [
            'title'     => 'Editar Sexo: ' . esc($sexo['nombre']),
            'module'    => 'sexo',
            'sexo'      => $sexo,
            'currentId' => (int)$id,
            'errors'    => session()->getFlashdata('errors') ?? [],
        ];

        return view('sexo/sexo_edit', $data);
    }

    public function update($id)
    {
        $sexo = $this->sexoModel->find((int)$id);
        if (!$sexo) {
            return redirect()->to(base_url('sexo'))->with('error', 'Registro no encontrado.');
        }

        $postData = [
            'nombre' => trim($this->request->getPost('nombre') ?? ''),
        ];

        if (!$this->sexoModel->update((int)$id, $postData)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->sexoModel->errors());
        }

        return redirect()->to(base_url('sexo/actual/' . $id))
                         ->with('success', '¡Registro de Sexo actualizado correctamente!');
    }

    public function delete($id)
    {
        $sexo = $this->sexoModel->find((int)$id);
        if (!$sexo) {
            return redirect()->to(base_url('sexo'))->with('error', 'Registro no encontrado.');
        }

        $this->sexoModel->delete((int)$id);

        $ultimo = $this->sexoModel->elultimo();
        $redirectUrl = $ultimo ? base_url('sexo/actual/' . $ultimo['idsexo']) : base_url('sexo');

        return redirect()->to($redirectUrl)
                         ->with('success', 'Sexo #' . $id . ' eliminado exitosamente.');
    }
}
