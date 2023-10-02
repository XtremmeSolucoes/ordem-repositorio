<?php

namespace App\Models;

use CodeIgniter\Model;

class ContaPagarModel extends Model
{
    protected $table            = 'contas_pagar';
    protected $returnType       = 'App\Entities\ContaPagar';
    protected $allowedFields    = [
        'fornecedor_id',
        'valor_conta',
        'data_vencimento',
        'situacao',
        'descricao',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'fornecedor_id'        => 'required',
        'valor_conta'     => 'required|greater_than[0]',
        'data_vencimento'     => 'required',
        'descricao'     => 'required',

    ];

    protected $validationMessages = [
        'fornecedor_id' => [
            'required' => 'O campo Fornecedor é obrigatório!',
        ],
        'valor_conta' => [
            'required' => 'O campo Valor é obrigatório!',
            'greater_tham' => 'O campo Valor tem que ser maior que zero!',
        ],
        'data_vencimento' => [
            'required' => 'O campo Data de Vencimento é obrigatório!',
        ],
        'descricao' => [
            'required' => 'O campo Descrição é obrigatório!',
        ],

    ];

    // Callbacks
    protected $beforeInsert   = ['removeVirgulaValores'];
    protected $beforeUpdate   = ['removeVirgulaValores'];

    protected function removeVirgulaValores(array $data){

        if(isset($data['data']['valor_conta'])){

            $data['data']['valor_conta'] = str_replace(",", "", $data['data']['valor_conta']);
            
        }

        return $data;
    }

    public function recuperaContasPagar()
    {
        $atributos = [
            'fornecedores.razao',
            'fornecedores.cnpj',
            'contas_pagar.*',
        ];

        return $this->select($atributos)
            ->join('fornecedores', 'fornecedores.id = contas_pagar.fornecedor_id')
            ->orderBy('contas_pagar.situacao', 'ASC')
            ->findAll();
    }

    public function buscaContaOu404(int $id = null)
    {
        if ($id === null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos a Conta a Pagar $id");
        }

        $atributos = [
            'fornecedores.razao',
            'fornecedores.cnpj',
            'contas_pagar.*',
        ];

        $conta = $this->select($atributos)
            ->join('fornecedores', 'fornecedores.id = contas_pagar.fornecedor_id')
            ->find($id);


        if ($conta === null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos a Conta a Pagar $id");
        }

        return $conta;
    }
}
