<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table            = 'itens';
    protected $returnType       = 'App\Entities\Item';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'codigo_interno',
        'nome',
        'marca',
        'modelo',
        'preco_custo',
        'preco_venda',
        'estoque',
        'controla_estoque',
        'tipo',
        'ativo',
        'descricao',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'nome'       => 'required|min_length[5]|max_length[230]|is_unique[itens.nome,id,{id}]',
        'marca'        => 'max_length[50]',
        'modelo'          => 'max_length[100]',
        'preco_venda'       => 'required',
        'descricao'    => 'required',               
    ];
    
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome é obrigatório!',
            'min_length' => 'O campo Nome precisa ter no minimo 5 caractéres!',
            'max_length' => 'O campo Nome precisa ter no máximo 230 caractéres!',
            'is_unique' => 'Esse Nome já está em uso, por favor informe um Nome diferente!',
        ],
        'marca' => [
            'max_length' => 'O campo Marca precisa ter no máximo 50 caractéres!', 
        ],
        'modelo' => [
            'max_length' => 'O campo Modelo precisa ter no máximo 100 caractéres!', 
        ],
        'preco_venda' => [
            'required' => 'O campo Preço de Venda é obrigatório!',           
        ],
        'descricao' => [
            'required' => 'O campo Descrição é obrigatório!',
        ],           
    ];

    // Callbacks
    protected $beforeInsert   = ['removeVirgulaValores'];
    protected $beforeUpdate   = ['removeVirgulaValores'];

    protected function removeVirgulaValores(array $data){

        if(isset($data['data']['preco_custo'])){

            $data['data']['preco_custo'] = str_replace(",", "", $data['data']['preco_custo']);
            
        }

        if(isset($data['data']['preco_venda'])){

            $data['data']['preco_venda'] = str_replace(",", "", $data['data']['preco_venda']);
            
        }

        return $data;
    }


    /**
     * Método que cria o codigo Interno do iten na hora do cadastro
     * @return string
     */

    public function gereCodigoInternoItem() : string 
    {
        do {
            $codigoInterno = random_string('numeric', 15);

            $this->where('codigo_interno', $codigoInterno);

        } while ($this->countAllResults() > 1);

        return $codigoInterno;
    }
}
