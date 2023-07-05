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
}
