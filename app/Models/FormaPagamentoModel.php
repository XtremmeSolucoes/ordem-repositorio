<?php

namespace App\Models;

use CodeIgniter\Model;

class FormaPagamentoModel extends Model
{
    protected $table            = 'formas_pagamentos';
    protected $returnType       = 'App\Entities\FormaPagamento';
    protected $allowedFields    = [
        'nome',
        'descricao',
        'ativo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules      = [

        'nome'        => 'required|max_length[120]|is_unique[formas_pagamentos.nome,id,{id}]',
        'descricao'     => 'required|max_length[240]',

    ];
    protected $validationMessages   = [

        'nome' => [
            'required' => 'O campo Nome é obrigatório!',          
            'max_length' => 'O campo Nome precisa ter no máximo 120 caractéres!',
            'is_unique' => 'Esse Nome já está cadastrado para outra Forma de Pagamento, por favor escolha um Nome diferente!',
        ],
        'descricao' => [
            'required' => 'O campo Descrição é obrigatório!',
            'max_length' => 'O campo Descrição precisa ter no máximo 240 caractéres!', 
        ],

    ];
}
