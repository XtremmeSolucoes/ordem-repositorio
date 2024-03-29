<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Fornecedor extends Entity
{
    protected $dates   = 
    [
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
}
