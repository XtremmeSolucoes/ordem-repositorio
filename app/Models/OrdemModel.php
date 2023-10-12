<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdemModel extends Model
{
    protected $table            = 'ordens';
    protected $returnType       = 'App\Entities\Ordem';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'cliente_id',
        'codigo',
        'forma_pagamento',
        'situacao',
        'itens',
        'valor_produtos',
        'valor_servicos',
        'valor_desconto',
        'valor_ordem',
        'equipamento',
        'defeito',
        'observacoes',
        'parecer_tecnico',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'cliente_id'        => 'required',
        'codigo'     => 'required',
        'equipamento'     => 'required',

    ];

    protected $validationMessages = [];


    public function recuperaOrdens()
    {
        $atributos = [
            'ordens.codigo',
            'ordens.criado_em',
            'ordens.situacao',
            'cliente.nome',
            'cliente.cpf',
        ];

        return $this->select($atributos)
                    ->join('clientes', 'clientes.id = ordens.cliente_id')
                    ->orderBy('ordens.situacao', 'ASC')
                    ->findAll();
    }


    /**
     * Método que cria o codigo Interno da ordem de serviço na hora do cadastro
     * @return string
     */

     public function gereCodigoOrdem() : string 
     {
         do {
             $codigo = strtoupper(random_string('alnum', 20));
 
             $this->select('codigo')->where('codigo', $codigo);
 
         } while ($this->countAllResults() > 1);
 
         return $codigo;
     }
}
