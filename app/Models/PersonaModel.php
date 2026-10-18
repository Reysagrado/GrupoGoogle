<?php

namespace App\Models;

use CodeIgniter\Model;

class PersonaModel extends Model
{
    use NavigableTrait;

    protected $table            = 'persona';
    protected $primaryKey       = 'idpersona';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'cedula',
        'nombres',
        'apellidos',
        'fechanacimiento',
        'idsexo',
        'idestadocivil',
    ];

    protected $validationRules = [
        'cedula'          => 'required|min_length[5]|max_length[20]|is_unique[persona.cedula,idpersona,{idpersona}]',
        'nombres'         => 'required|min_length[2]|max_length[100]',
        'apellidos'       => 'required|min_length[2]|max_length[100]',
        'fechanacimiento' => 'permit_empty|valid_date',
        'idsexo'          => 'permit_empty|is_natural_no_zero',
        'idestadocivil'   => 'permit_empty|is_natural_no_zero',
    ];

    protected $validationMessages = [
        'cedula' => [
            'required'   => 'El número de cédula o identificación es obligatorio.',
            'min_length' => 'La cédula debe contener al menos 5 caracteres.',
            'max_length' => 'La cédula no puede superar los 20 caracteres.',
            'is_unique'  => 'Este número de cédula ya se encuentra registrado.',
        ],
        'nombres' => [
            'required'   => 'Los nombres son obligatorios.',
            'min_length' => 'Los nombres deben tener al menos 2 caracteres.',
            'max_length' => 'Los nombres no pueden superar los 100 caracteres.',
        ],
        'apellidos' => [
            'required'   => 'Los apellidos son obligatorios.',
            'min_length' => 'Los apellidos deben tener al menos 2 caracteres.',
            'max_length' => 'Los apellidos no pueden superar los 100 caracteres.',
        ],
        'fechanacimiento' => [
            'valid_date' => 'La fecha de nacimiento debe ser una fecha válida (AAAA-MM-DD).',
        ],
    ];

    /**
     * Obtener una persona por ID con los nombres de su sexo y estado civil
     */
    public function getPersonaDetail($id)
    {
        return $this->select('persona.*, sexo.nombre AS sexo_nombre, estadocivil.nombre AS estadocivil_nombre')
                    ->join('sexo', 'sexo.idsexo = persona.idsexo', 'left')
                    ->join('estadocivil', 'estadocivil.idestadocivil = persona.idestadocivil', 'left')
                    ->where('persona.idpersona', (int)$id)
                    ->first();
    }

    /**
     * Obtener listado general de personas con búsqueda y ordenamiento
     */
    public function getPersonasOrdenadas($search = null)
    {
        $builder = $this->select('persona.*, sexo.nombre AS sexo_nombre, estadocivil.nombre AS estadocivil_nombre,
                                  (SELECT COUNT(*) FROM direccion d WHERE d.idpersona = persona.idpersona) AS total_direcciones,
                                  (SELECT COUNT(*) FROM correoelectronico c WHERE c.idpersona = persona.idpersona) AS total_correos')
                        ->join('sexo', 'sexo.idsexo = persona.idsexo', 'left')
                        ->join('estadocivil', 'estadocivil.idestadocivil = persona.idestadocivil', 'left')
                        ->orderBy('persona.idpersona', 'DESC');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('persona.cedula', $search)
                    ->orLike('persona.nombres', $search)
                    ->orLike('persona.apellidos', $search)
                    ->orLike('sexo.nombre', $search)
                    ->orLike('estadocivil.nombre', $search)
                    ->groupEnd();
        }

        return $builder->findAll();
    }

    /**
     * Resumen estadístico para el Dashboard
     */
    public function getDashboardStats()
    {
        $db = \Config\Database::connect();

        $totalPersonas    = $this->countAllResults();
        $totalSexos       = $db->table('sexo')->countAllResults();
        $totalEC          = $db->table('estadocivil')->countAllResults();
        $totalDirecciones = $db->table('direccion')->countAllResults();
        $totalCorreos     = $db->table('correoelectronico')->countAllResults();

        // Distribución por sexo
        $distSexo = $db->table('persona')
                       ->select('COALESCE(sexo.nombre, "Sin especificar") AS categoria, COUNT(persona.idpersona) AS total')
                       ->join('sexo', 'sexo.idsexo = persona.idsexo', 'left')
                       ->groupBy('categoria')
                       ->orderBy('total', 'DESC')
                       ->get()->getResultArray();

        // Distribución por estado civil
        $distEC = $db->table('persona')
                     ->select('COALESCE(estadocivil.nombre, "Sin especificar") AS categoria, COUNT(persona.idpersona) AS total')
                     ->join('estadocivil', 'estadocivil.idestadocivil = persona.idestadocivil', 'left')
                     ->groupBy('categoria')
                     ->orderBy('total', 'DESC')
                     ->get()->getResultArray();

        // Últimas 5 personas registradas
        $ultimasPersonas = $this->select('persona.*, sexo.nombre AS sexo_nombre, estadocivil.nombre AS estadocivil_nombre')
                                ->join('sexo', 'sexo.idsexo = persona.idsexo', 'left')
                                ->join('estadocivil', 'estadocivil.idestadocivil = persona.idestadocivil', 'left')
                                ->orderBy('persona.idpersona', 'DESC')
                                ->limit(5)
                                ->findAll();

        return [
            'totalPersonas'    => $totalPersonas,
            'totalSexos'       => $totalSexos,
            'totalEC'          => $totalEC,
            'totalDirecciones' => $totalDirecciones,
            'totalCorreos'     => $totalCorreos,
            'distSexo'         => $distSexo,
            'distEC'           => $distEC,
            'ultimasPersonas'  => $ultimasPersonas,
        ];
    } 
    
    /**
     * Calcula la edad en años a partir de la fecha de nacimiento
     */
    public function calcularEdad(?string $fechaNacimiento): ?int
    {
        if (empty($fechaNacimiento)) {
            return null;
        }

        return (new \DateTime($fechaNacimiento))->diff(new \DateTime('today'))->y;
    }
}
