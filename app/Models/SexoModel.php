<?php

namespace App\Models;

use CodeIgniter\Model;

class SexoModel extends Model
{
    use NavigableTrait;

    protected $table            = 'sexo';
    protected $primaryKey       = 'idsexo';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nombre'];
    protected $beforeInsert = ['normalizarNombre'];
    protected $beforeUpdate = ['normalizarNombre'];

    protected $validationRules = [
        'nombre' => 'required|min_length[2]|max_length[50]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required'   => 'El nombre del sexo es obligatorio.',
            'min_length' => 'Debe contener al menos 2 caracteres.',
            'max_length' => 'No puede superar los 50 caracteres.',
        ],
    ];

    /**
     * Obtener listado con conteo de personas asociadas
     */
    public function getSexosConConteo($search = null)
    {
        $builder = $this->select('sexo.*, COUNT(persona.idpersona) AS total_personas')
                        ->join('persona', 'persona.idsexo = sexo.idsexo', 'left')
                        ->groupBy('sexo.idsexo')
                        ->orderBy('sexo.idsexo', 'ASC');

        if (!empty($search)) {
            $builder->like('sexo.nombre', $search);
        }

        return $builder->findAll();
    }

    /**
     * Quita espacios sobrantes y pone mayúscula inicial al nombre
     */
    protected function normalizarNombre(array $data): array
    {
        if (isset($data['data']['nombre'])) {
            $data['data']['nombre'] = mb_convert_case(trim($data['data']['nombre']), MB_CASE_TITLE, 'UTF-8');
        }

        return $data;
    }

}
