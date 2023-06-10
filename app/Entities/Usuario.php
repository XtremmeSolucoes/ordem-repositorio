<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Usuario extends Entity
{
    
    protected $dates   = [
        'criado_em', 
        'atualizado_em', 
        'deletado_em',
    ];

    /**
     * Método que verifica se a senha é valida
     *  @param string $password
     *  @return boolean
     */

    public function verificaPassword(string $password): bool
    {

        return password_verify($password, $this->password_hash);
        
    }
    
}
