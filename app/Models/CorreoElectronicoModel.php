<?php

namespace App\Models;

use CodeIgniter\Model;

class CorreoElectronicoModel extends Model
{
    use NavigableTrait;

    protected $table            = 'correoelectronico';
    protected $primaryKey       = 'idcorreoelectronico';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'idpersona',
        'correo',
        'nombre',
        'tipo',
        'fechaoptencion',
    ];
    
    protected $beforeInsert = ['normalizarCorreo'];
    protected $beforeUpdate = ['normalizarCorreo'];

    protected $validationRules = [
        'idpersona'      => 'required|is_natural_no_zero',
        'correo'         => 'required|valid_email|max_length[150]',
        'tipo'           => 'permit_empty|max_length[50]',
        'fechaoptencion' => 'permit_empty|valid_date',
    ];

    protected $validationMessages = [
        'idpersona' => [
            'required'           => 'Debe seleccionar una persona a quien pertenezca el correo electrónico.',
            'is_natural_no_zero' => 'Seleccione una persona válida.',
        ],
        'correo' => [
            'required'    => 'La dirección de correo electrónico es obligatoria.',
            'valid_email' => 'Ingrese una dirección de correo electrónico con formato válido (ejemplo: usuario@correo.com).',
            'max_length'  => 'El correo no puede superar los 150 caracteres.',
        ],
        'fechaoptencion' => [
            'valid_date'  => 'La fecha de obtención debe ser una fecha válida (AAAA-MM-DD).',
        ],
    ];

    /**
     * Obtener listado con datos de la persona asociada
     */
    public function getCorreosWithPersona($search = null, $idpersona = null)
    {
        $builder = $this->select('correoelectronico.*, persona.cedula, persona.nombres, persona.apellidos')
                        ->join('persona', 'persona.idpersona = correoelectronico.idpersona', 'inner')
                        ->orderBy('correoelectronico.idcorreoelectronico', 'DESC');

        if (!empty($idpersona)) {
            $builder->where('correoelectronico.idpersona', (int)$idpersona);
        }

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('correoelectronico.correo', $search)
                    ->orLike('correoelectronico.tipo', $search)
                    ->orLike('persona.cedula', $search)
                    ->orLike('persona.nombres', $search)
                    ->orLike('persona.apellidos', $search)
                    ->groupEnd();
        }

        return $builder->findAll();
    }

    /**
     * Obtener un correo por ID con datos de la persona
     */
    public function getCorreoDetail($id)
    {
        return $this->select('correoelectronico.*, persona.cedula, persona.nombres, persona.apellidos')
                    ->join('persona', 'persona.idpersona = correoelectronico.idpersona', 'inner')
                    ->where('correoelectronico.idcorreoelectronico', (int)$id)
                    ->first();
    }

    /**
     * Obtener todos los correos pertenecientes a una persona
     */
    public function getCorreosByPersona($idpersona)
    {
        return $this->where('idpersona', (int)$idpersona)
                    ->orderBy('idcorreoelectronico', 'DESC')
                    ->findAll();
    }
    
    /**
     * Guarda el correo sin espacios y en minúsculas
     */
    protected function normalizarCorreo(array $data): array
    {
        if (isset($data['data']['correo'])) {
            $data['data']['correo'] = strtolower(trim($data['data']['correo']));
        }

        return $data;
    }
}
