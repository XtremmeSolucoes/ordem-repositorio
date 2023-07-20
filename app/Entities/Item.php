<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Item extends Entity
{
    
    protected $dates   = [
        'criado_em', 
        'atualizado_em', 
        'deletado_em'
    ];
    
    public function exibeSituacao()
    {

        if($this->ativo == true)
        {
            return '<i class="fa fa-unlock-alt text-success" ></i>&nbsp;Ativo';
        }
        if($this->ativo == false)
        {
            return '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo';
        }
    }

    public function exibeTipo()
    {
        $tipoItem = "";

        if($this->tipo === 'produto')
        {
            $tipoItem = '<i class="fa fa-archive text-success" aria-hidden="true"></i>&nbsp;Produto';

        }else{

            $tipoItem = '<i class="fa fa-wrench text-white" aria-hidden="true"></i>&nbsp;Serviço';
        }
        
        return $tipoItem;
    }

    public function exibeEstoque()
    {
        return ($this->tipo === 'produto' ? $this->estoque: 'Não se aplica!');
    }

    public function recuperaAtributosAlterados() : string
    {
        $atributosAlterados = [];

        if($this->hasChanged('nome')){

            $atributosAlterados['nome'] = "O nome foi alterado para $this->nome";
        }

        if($this->hasChanged('preco_custo')){

            $atributosAlterados['preco_custo'] = "O Preço de custo foi alterado para $this->preco_custo";
        }

        if($this->hasChanged('preco_venda')){

            $atributosAlterados['preco_venda'] = "O Preço de venda foi alterado para $this->preco_venda";
        }

        if($this->hasChanged('estoque')){

            $atributosAlterados['estoque'] = "O Preço de venda foi alterado para $this->estoque";
        }

        if($this->hasChanged('descricao')){

            $atributosAlterados['descricao'] = "A descrição foi alterada para $this->descricao";
        }

        if($this->hasChanged('descricao')){

            $atributosAlterados['descricao'] = "A descrição foi alterada para $this->descricao";
        }

        if($this->hasChanged('controla_estoque')){

            if($this->controla_estoque === true){

                $atributosAlterados['controla_estoque'] = "O controle de estoque foi ativado";

            }else {

                $atributosAlterados['controla_estoque'] = "O controle de estoque foi desativado";

            }

            
        }

        if($this->hasChanged('ativo')){

            if($this->ativo === true){

                $atributosAlterados['ativo'] = "O item foi ativado";

            }else {

                $atributosAlterados['ativo'] = "O item foi desativado";

            }

            
        }

        return serialize($atributosAlterados);
    }
}
