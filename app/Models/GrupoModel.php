<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoModel extends Model
{
    protected $table            = 'grupos';
    protected $returnType       = 'App\Entities\Grupo';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nome', 'descricao', 'exibir'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'nome'        => 'required|max_length[60]|is_unique[grupos.nome,id,{id}]',
        'descricao'     => 'required|max_length[240]',
        
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome é obrigatório!',          
            'max_length' => 'O campo Nome precisa ter no máximo 60 caractéres!',
            'is_unique' => 'Esse Nome já está cadastrado para outro Grupo, por favor escolha um Nome diferente!',
        ],
        'descricao' => [
            'required' => 'O campo Descrição é obrigatório!',
            'max_length' => 'O campo Descrição precisa ter no máximo 240 caractéres!', 
        ],

    ];



}
