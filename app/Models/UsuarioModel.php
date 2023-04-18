<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpParser\Node\Expr\Isset_;

class UsuarioModel extends Model
{
   
    protected $table            = 'usuarios';
    protected $returnType       = 'App\Entities\Usuario';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'nome',
        'email',
        'password',
        'reset_hash',
        'reset_expira_em',
        'imagem',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'nome'     => 'required|min_length[3]|max_length[120]',
        'email'        => 'required|valid_email|max_length[60]|is_unique[usuarios.email,id,{id}]',
        'password'     => 'required|min_length[8]',
        'password_confirmation' => 'required_with[password]|matches[password]',
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome é obrigatório!',
            'min_length' => 'O campo Nome precisa ter no minimo três caractéres!',
            'max_length' => 'O campo Nome precisa ter no máximo 120 caractéres!',
        ],
        'email' => [
            'required' => 'O campo Email é obrigatório!',
            'valid_email' => 'O campo E-mail precisa de um email válido!',
            'max_length' => 'O campo Email precisa ter no máximo 60 caractéres!', 
            'is_unique' => 'Esse Email já está cadastrado para outro usuário, por favor escolha um email diferente!',
        ],
        'password_confirmation' => [
            'required_with' => 'Por favor confirme a sua senha!',
            'matches' => 'As senhas não são iguais, repita a senha escolhida acima!',
        ],

    ];

    // Callbacks
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data){

        if(isset($data['data']['password'])){

            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']);
            unset($data['data']['password_confirmation']);
        }

        return $data;
    }
}
