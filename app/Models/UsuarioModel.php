<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['nombre_completo', 'usuario', 'password'];

    // Validation
    protected $validationRules = [
        'nombre_completo' => 'required|regex_match[/^[a-zA-Z\s]+$/]|max_length[50]',
        'usuario' => 'required|regex_match[/^[a-zA-Z\s]+$/]|max_length[50]',
        'password' => 'required',
    ];

    protected $validationMessages = [
        'nombre_completo' => [
            'required' => INPUT_REQUIRED,
            'regex_match' => INPUT_ONLY_LETTERS,
            'max_length' => INPUT_MAX_LENGTH_50
        ],
        'usuario' => [
            'required' => INPUT_REQUIRED,
            'regex_match' => INPUT_ONLY_LETTERS,
            'max_length' => INPUT_MAX_LENGTH_50
        ],
        'password' => [
            'required' => INPUT_REQUIRED,
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function consultarUsuario($usuario)
    {
        $this->select('id, nombre_completo, usuario, password');
        $this->where('usuario', $usuario);
        return $this->first();
    }

    public function registroUsuario($nombre_completo, $usuario, $password)
    {
        $query = $this->db->query('SELECT registro_usuario(?, ?, ?) as nuevo_id', array($nombre_completo, $usuario, $password));
        $resultado = $query->getRow();

        if ($resultado && isset($resultado->nuevo_id)) {
            return $resultado->nuevo_id;
        } else {
            return ['error' => 'Error al registrar el usuario.'];
        }
    }
}
