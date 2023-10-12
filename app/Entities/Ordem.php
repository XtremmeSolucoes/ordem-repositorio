<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Ordem extends Entity
{
    protected $dates   = [
        'criado_em', 
        'atualizado_em', 
        'deletado_em'
    ];

    public function exibeSituacao()
    {

        if($this->situacao === 'aberta')
        {
            return '<i class="fa fa-unlock-alt text-warning" ></i>&nbsp;'. ucfirst($this->situacao);
        }

        if($this->situacao === 'encerrada')
        {
            return '<i class="fa fa-lock text-danger"></i>&nbsp;'. ucfirst($this->situacao);
        }

        if($this->situacao === 'aguardando')
        {
            return '<i class="fa fa-clock-o text-warning"></i>&nbsp;'. ucfirst($this->situacao);
        }

        if($this->situacao === 'nao_pago')
        {
            return '<i class="fa fa-clock-o text-danger"></i>&nbsp; Não pago';
        }

        if($this->situacao === 'cancelada')
        {
            return '<i class="fa fa-ban text-danger"></i>&nbsp;'. ucfirst($this->situacao);
        }
    }
}
