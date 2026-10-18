<?php

namespace App\Models;

use CodeIgniter\Model;

class DireccionModel extends Model
{
    use NavigableTrait;

    protected $table            = 'direccion';
    protected $primaryKey       = 'iddireccion';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['idpersona', 'direccion', 'nombre', 'tipo'];
    protected $beforeInsert = ['normalizarDireccion'];
    protected $beforeUpdate = ['normalizarDireccion'];
    protected $validationRules = [
        'idpersona' => 'required|is_natural_no_zero',
        'direccion' => 'required|min_length[3]|max_length[255]',
        'tipo'      => 'permit_empty|max_length[50]',
    ];

    protected $validationMessages = [
        'idpersona' => [
            'required'            => 'Debe seleccionar una persona a quien pertenezca la dirección.',
            'is_natural_no_zero'  => 'Seleccione una persona válida.',
        ],
        'direccion' => [
            'required'   => 'La dirección domiciliaria o laboral es obligatoria.',
            'min_length' => 'La dirección debe tener al menos 3 caracteres.',
            'max_length' => 'La dirección no puede superar los 255 caracteres.',
        ],
    ];

    /**
     * Obtener listado con datos de la persona asociada
     */
    public function getDireccionesWithPersona($search = null, $idpersona = null)
    {
        $builder = $this->select('direccion.*, persona.cedula, persona.nombres, persona.apellidos')
                        ->join('persona', 'persona.idpersona = direccion.idpersona', 'inner')
                        ->orderBy('direccion.iddireccion', 'DESC');

        if (!empty($idpersona)) {
            $builder->where('direccion.idpersona', (int)$idpersona);
        }

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('direccion.direccion', $search)
                    ->orLike('direccion.tipo', $search)
                    ->orLike('persona.cedula', $search)
                    ->orLike('persona.nombres', $search)
                    ->orLike('persona.apellidos', $search)
                    ->groupEnd();
        }

        return $builder->findAll();
    }

    /**
     * Obtener una dirección por ID con datos de la persona
     */
    public function getDireccionDetail($id)
    {
        return $this->select('direccion.*, persona.cedula, persona.nombres, persona.apellidos')
                    ->join('persona', 'persona.idpersona = direccion.idpersona', 'inner')
                    ->where('direccion.iddireccion', (int)$id)
                    ->first();
    }

    /**
     * Obtener todas las direcciones pertenecientes a una persona
     */
    public function getDireccionesByPersona($idpersona)
    {
        return $this->where('idpersona', (int)$idpersona)
                    ->orderBy('iddireccion', 'DESC')
                    ->findAll();
    }

    /**
     * Quita espacios sobrantes y pone mayúscula en la primera letra de la dirección
     */
    protected function normalizarDireccion(array $data): array
    {
        if (isset($data['data']['direccion'])) {
            $texto = trim($data['data']['direccion']);
            $data['data']['direccion'] = mb_strtoupper(mb_substr($texto, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($texto, 1, null, 'UTF-8');
        }

        return $data;
    }
}
