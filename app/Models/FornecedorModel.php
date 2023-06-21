<?php

namespace App\Models;

use CodeIgniter\Model;

class FornecedorModel extends Model
{
    protected $table            = 'fornecedores';
    protected $returnType       = 'App\Entities\Fornecedor';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'razao',
        'cnpj',
        'ie',
        'email',
        'telefone',
        'cep',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'ativo',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

   // Validation
protected $validationRules = [
    'razao'       => 'required|min_length[5]|max_length[230]|is_unique[fornecedores.razao,id,{id}]',
    'cnpj'        => 'required|validaCNPJ|max_length[25]|is_unique[fornecedores.cnpj,id,{id}]',
    'ie'          => 'required|max_length[25]|is_unique[fornecedores.ie,id,{id}]',
    'email'       => 'required|valid_email|max_length[60]|is_unique[fornecedores.email,id,{id}]',
    'telefone'    => 'required|min_length[10]|max_length[18]|is_unique[fornecedores.telefone,id,{id}]',
    'cep'         => 'required|min_length[8]|max_length[9]',
    'endereco'    => 'required|min_length[10]|max_length[120]',
    'numero'      => 'max_length[20]',
    'bairro'    => 'required|min_length[5]|max_length[120]',
    'cidade'    => 'required|min_length[5]|max_length[28]',
    'estado'    => 'required|min_length[2]|max_length[2]',
    
];

protected $validationMessages = [
    'razao' => [
        'required' => 'O campo Razão Social é obrigatório!',
        'min_length' => 'O campo Nome precisa ter no minimo 5 caractéres!',
        'max_length' => 'O campo Nome precisa ter no máximo 230 caractéres!',
        'is_unique' => 'Essa Razão Social já está em uso, por favor informe uma Razão Social diferente!',
    ],
    'cnpj' => [
        'required' => 'O campo cnpj é obrigatório!',
        'validaCNPJ' => 'O campo CNPJ precisa de um CNPJ válido!',
        'max_length' => 'O campo Email precisa ter no máximo 25 caractéres!', 
        'is_unique' => 'Essa Inscrição Estadual já está em uso, por favor informe um CNPJ diferente!',
    ],
    'ie' => [
        'required' => 'O campo Inscrição Estadual é obrigatório!',
        'max_length' => 'O campo Email precisa ter no máximo 25 caractéres!', 
        'is_unique' => 'Esse CNPJ já está em uso, por favor informe uma Inscrição Social diferente!',
    ],
    'email' => [
        'required' => 'O campo Email é obrigatório!',
        'valid_email' => 'O campo E-mail precisa de um email válido!',
        'max_length' => 'O campo Email precisa ter no máximo 60 caractéres!', 
        'is_unique' => 'Esse Email já está em uso, por favor informe um email diferente!',
    ],
    'telefone' => [
        'required' => 'O campo Telefone é obrigatório!',
        'min_length' => 'O campo Nome precisa ter no minimo 10 caractéres!',
        'max_length' => 'O campo Email precisa ter no máximo 18 caractéres!', 
        'is_unique' => 'Esse Telefone já está em uso, por favor informe um telefone diferente!',
    ],
    'cep' => [
        'required' => 'O campo CEP é obrigatório!',
        'min_length' => 'O campo Nome precisa ter no minimo 9 caractéres!',
        'max_length' => 'O campo CEP precisa ter no máximo 9 caractéres!', 
    ],
    'endereco' => [
        'required' => 'O campo Endereço é obrigatório!',
        'min_length' => 'O campo Nome precisa ter no minimo 10 caractéres!',
        'max_length' => 'O campo Email precisa ter no máximo 120 caractéres!', 
    ],
    'telefone' => [
        'required' => 'O campo Telefone é obrigatório!',
        'min_length' => 'O campo Nome precisa ter no minimo 10 caractéres!',
        'max_length' => 'O campo Email precisa ter no máximo 18 caractéres!', 
        'is_unique' => 'Esse Telefone já está em uso, por favor informe um telefone diferente!',
    ],
    'numero' => [
        'max_length' => 'O campo Email precisa ter no máximo 20 caractéres!', 

    ],
    'bairro' => [
        'required' => 'O campo Bairro é obrigatório!',
        'min_length' => 'O campo Nome precisa ter no minimo 5 caractéres!',
        'max_length' => 'O campo Email precisa ter no máximo 120 caractéres!', 
    ],
    'cidade' => [
        'required' => 'O campo Cidade é obrigatório!',
        'min_length' => 'O campo Nome precisa ter no minimo 5 caractéres!',
        'max_length' => 'O campo Email precisa ter no máximo 28 caractéres!', 
    ],
    'estado' => [
        'required' => 'O campo Estado é obrigatório!',
        'min_length' => 'O campo Nome precisa ter no minimo 02 caractéres!',
        'max_length' => 'O campo Email precisa ter no máximo 02 caractéres!', 
    ],

];
}
