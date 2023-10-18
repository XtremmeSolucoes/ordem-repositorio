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
            'clientes.nome',
            'clientes.cpf',
        ];

        return $this->select($atributos)
            ->join('clientes', 'clientes.id = ordens.cliente_id')
            ->orderBy('ordens.id', 'DESC')
            ->findAll();
    }

    
    /**
     * Método responsável por recuperar a ordem de serviço
     * 
     * @param string|null $codigo
     * @return object|PageNotFoundException
     */
    public function buscaOrdemOu404(string $codigo)
    {

        if ($codigo === null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos a OrDEM $codigo");
        }

        $atributos = [
            'ordens.*',
            'u_aber.id AS usuario_abertura_id', // ID do usuário que abriu a Ordem
            'u_aber.nome AS usuario_abertura',  // NOME do usuário que abriu a Ordem

            'u_resp.id AS usuario_responsavel_id',  // ID do usuário que responsável pela Ordem
            'u_resp.nome AS usuario_responsavel',  // NOME do usuário que responsável pela Ordem

            'u_ence.id AS usuario_encerramento_id',  // ID do usuário que encerrou a Ordem
            'u_ence.nome AS usuario_encerramento',  // NOME do usuário que encerrou a Ordem

            'clientes.usuario_id AS cliente_usuario_id', //Usaremos para o acesso do cliente ao sistema
            'clientes.nome', //obrigatório para o gerencianet
            'clientes.cpf', //obrigatório para o gerencianet
            'clientes.telefone', //obrigatório para o gerencianet
            'clientes.email', //obrigatório para o gerencianet
        ];

        $ordem = $this->select($atributos)
                      ->join('ordens_responsaveis', 'ordens_responsaveis.ordem_id = ordens.id')
                      ->join('clientes', 'clientes.id = ordens.cliente_id')
                      ->join('usuarios AS u_cliente', 'u_cliente.id = clientes.usuario_id')
                      ->join('usuarios AS u_aber', 'u_aber.id = ordens_responsaveis.usuario_abertura_id')
                      ->join('usuarios AS u_resp', 'u_resp.id = ordens_responsaveis.usuario_responsavel_id', 'LEFT')
                      ->join('usuarios AS u_ence', 'u_ence.id = ordens_responsaveis.usuario_encerramento_id', 'LEFT')
                      ->where('ordens.codigo', $codigo)
                      ->first();


        if ($codigo === null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos a Ordem $codigo");
        }

        return $ordem;
    }


    /**
     * Método que cria o codigo Interno da ordem de serviço na hora do cadastro
     * @return string
     */

    public function gereCodigoOrdem(): string
    {
        do {
            $codigo = strtoupper(random_string('alnum', 20));

            $this->select('codigo')->where('codigo', $codigo);
        } while ($this->countAllResults() > 1);

        return $codigo;
    }
}
