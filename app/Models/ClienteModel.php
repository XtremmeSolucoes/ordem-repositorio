<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table            = 'clientes';
    protected $returnType       = 'App\Entities\Cliente';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = 
    [
       'usuario_id', 
       'nome', 
       'cpf', 
       'telefone', 
       'email',
       'cep', 
       'endereco', 
       'numero', 
       'bairro', 
       'cidade', 
       'estado', 
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

     // Validation
protected $validationRules = [
    'nome'       => 'required|min_length[5]|max_length[230]',
    'cpf'        => 'required|validaCPF|exact_length[14]|is_unique[clientes.cpf,id,{id}]',
    'telefone'    => 'required|exact_length[15]|is_unique[clientes.telefone,id,{id}]',
    'email'       => 'required|valid_email|max_length[60]|is_unique[clientes.email,id,{id}]',
    'email'       => 'is_unique[usuarios.email,id,{id}]',
    'cep'         => 'required|exact_length[9]',
    'endereco'    => 'required|min_length[10]|max_length[120]',
    'numero'      => 'max_length[20]',
    'bairro'    => 'required|min_length[5]|max_length[120]',
    'cidade'    => 'required|min_length[5]|max_length[28]',
    'estado'    => 'required|min_length[2]|max_length[2]',
    
];

protected $validationMessages = [
    'nome' => [
        'required' => 'O campo Nome é obrigatório!',
        'min_length' => 'O campo Nome precisa ter no minimo 5 caractéres!',
        'max_length' => 'O campo Nome precisa ter no máximo 230 caractéres!',        
    ],
    'cpf' => [
        'required' => 'O campo CPF é obrigatório!',
        'validaCPF' => 'O campo CPF precisa de um CPF válido!',
        'max_length' => 'O campo CPF precisa ter no máximo 14 caractéres!', 
        'is_unique' => 'Esse CPF já está em uso, por favor informe um CPF diferente!',
    ],
    'email' => [
        'required' => 'O campo Email é obrigatório!',
        'valid_email' => 'O campo E-mail precisa de um email válido!',
        'max_length' => 'O campo Email precisa ter no máximo 60 caractéres!', 
        'is_unique' => 'Esse Email já está em uso, por favor informe um email diferente!',
    ],
    'telefone' => [
        'required' => 'O campo Telefone é obrigatório!',
        'min_length' => 'O campo Telefone precisa ter no minimo 15 caractéres!',
        'max_length' => 'O campo Telefone precisa ter no máximo 15 caractéres!', 
        'is_unique' => 'Esse Telefone já está em uso, por favor informe um telefone diferente!',
    ],
    'cep' => [
        'required' => 'O campo CEP é obrigatório!',
        'min_length' => 'O campo CEP precisa ter no minimo 9 caractéres!',
        'max_length' => 'O campo CEP precisa ter no máximo 9 caractéres!', 
    ],
    'endereco' => [
        'required' => 'O campo Endereço é obrigatório!',
        'min_length' => 'O campo Endereço precisa ter no minimo 10 caractéres!',
        'max_length' => 'O campo Endereço precisa ter no máximo 120 caractéres!', 
    ],
    'numero' => [
        'max_length' => 'O campo Número precisa ter no máximo 20 caractéres!', 

    ],
    'bairro' => [
        'required' => 'O campo Bairro é obrigatório!',
        'min_length' => 'O campo Bairro precisa ter no minimo 5 caractéres!',
        'max_length' => 'O campo Bairro precisa ter no máximo 120 caractéres!', 
    ],
    'cidade' => [
        'required' => 'O campo Cidade é obrigatório!',
        'min_length' => 'O campo Cidade precisa ter no minimo 5 caractéres!',
        'max_length' => 'O campo Cidade precisa ter no máximo 28 caractéres!', 
    ],
    'estado' => [
        'required' => 'O campo Estado é obrigatório!',
        'min_length' => 'O campo Estado precisa ter no minimo 02 caractéres!',
        'max_length' => 'O campo Estado precisa ter no máximo 02 caractéres!', 
    ],

];

   
}
