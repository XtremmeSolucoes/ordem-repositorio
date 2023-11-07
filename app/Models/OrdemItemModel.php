<?php

namespace App\Models;

use CodeIgniter\Model;


/**
 * Método que recupera os itens da ordem de serviço
 * @param integer $ordem_id
 * @return array|null
 */
class OrdemItemModel extends Model
{
    protected $table            = 'ordens_itens';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'ordem_id',
        'item_id',
        'item_quantidade',
    ];

    public function recuperaItensDaOrdem(int $ordem_id)
    {
        $atributos = [
            'itens.id',
            'itens.nome',
            'itens.preco_venda',
            'itens.tipo',
            'itens.controla_estoque',
            'ordens_itens.id AS id_principal',
            'ordens_itens.item_quantidade',
        ];

        return $this->select($atributos)
                    ->join('itens', 'itens.id = ordens_itens.item_id')
                    ->where('ordens_itens.ordem_id', $ordem_id)
                    ->groupBy('itens.nome')
                    ->orderBy('itens.tipo', 'ASC')
                    ->findAll();
    }

    public function atualizarQuantidadeItem(object $ordemItem)
    {
        return $this->set('item_quantidade', $ordemItem->item_quantidade)
                    ->where('id', $ordemItem->id)
                    ->update();

    }

}
