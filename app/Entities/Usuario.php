<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use App\Libraries\Token;

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

    /**
     * Método que valida se o usuário logado possui a permissão para vizualizar / acessar determinada rota.
     * 
     * @param string $permissao
     * @return boolean
     */

    public function temPermissaoPara(string $permissao) : bool 
    {
        //Se o usuário logado é um admim retornamos true
        if($this->is_admin == true)
        {
            return true;
        }

        //Se o usuário logado não for admin ou cleiente e  não tiver nenhuma permissão retornamos false
        if(empty($this->permissoes))
        {

            return false;

        }

        //Nesse ponto o usuário logado possui persissões,
        //Então podemos verificar tranquilamente
        if(in_array($permissao, $this->permissoes) == false)
        {

            return false;

        }

        //Retornamos o true o usuário logado tem as permissões necessarias 
        return true;
    }

    /**
     * Método que inicia a recuperação da senha
     * 
     * @return void
     */

    public function iniciaPasswordReset() : void 
    {
        $token = new Token();

        $this->reset_token = $token->getValue();

        $this->reset_hash = $token->getHash();

        $this->reset_expira_em = date('Y-m-d H:i:s', time() + 900);
    }

    /**
     * Método que finaliza o processo de redifinição de senha
     * 
     * @return void
     */

    public function finalizaPasswordReset() : void 
    {
        $this->reset_hash = null;
        $this->reset_expira_em = null;
    }
    
}
