<?php 

namespace App\Libraries;

class Autenticacao{

    private $usuario;
    private $usuarioModel;
    private $grupoUsuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
        $this->grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();
    }

    /**
     * Método que realiza o login na aplicação
     * 
     * @param string $email
     * @param string $password
     * @return boolean
     */

    public function login(string $email, string $password): bool
    {
        //Buscamos o usuário que irá logar
        $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);

        if($usuario === null){

            return false;

        }

        //Verifação se a senha é valida
        if($usuario->verificaPassword($password) == false)
        {
            return false;
        }

        //Verificação se o usuário está ativo para logar na aplicação
        if($usuario->ativo == false)
        {
            return false;
        }

        //logamos o usuário na aplicação
        $this->logaUsuario($usuario);


        //TUDO CERTO O USUÁRIO ESTÁ OK, PODE LOGAR 
        return true;


    }

    /**
     * Método de Logout
     * @return void
     */
    public function logout(): void
    {
        session()->destroy();
    }

    /**
     * Método pega o usuário Logado
     */
    public function pegaUsuarioLogado()
    {
        if ($this->usuario === null) 
        {
            $this->usuario = $this->pegaUsuarioDaSessao();
        }

        return $this->usuario;
    }

    /**
     * Método verifica se o usuáriuo ta logado
     * @return boolean
     */
    public function estaLogado(): bool
    {
        return $this->pegaUsuarioLogado() !== null;
    }

    //---------------- Métodos Privados ------------------------------------//

    /**
     * Método que insere na sessão o ID do usuário
     * @param object $usuario
     * @return void
     */

    private function logaUsuario(object $usuario): void
    {
        //recuperando a estancia da sessão
        $session = session();

        //geramos um novo ID da sessão antes de logar
        $_SESSION['__ci_last_regenerate'] = time();

        //setamos na sessão o ID do usuário
        $session->set('usuario_id', $usuario->id);
    }

    /**
     * Método que recupera da sessão e valida o usuário logado
     * @return null|object
     */
    private function pegaUsuarioDaSessao()
    {
        if(session()->has('usuario_id') == false)
        {
            return null;
        }

        //buscamos o usuário na base de dados
        $usuario = $this->usuarioModel->find(session()->get('usuario_id'));

        //validamos se o usuário existe e se está apto a logar na aplicação
        if($usuario == null || $usuario->ativo == false)
        {
            return null;
        }

        //Definimos as permissões do usuário logado 

        $usuario = $this->definePermissoesDoUsuarioLogado($usuario);

        //tudo ok, retornamos o usuário
        return $usuario;
    }

    /**
     * Método verifica se o usuario logado faz parte do grupo de administrador
     * @return boolean
     */

    private function isAdmin() : bool 
    {
        $grupoAdmin = 1;

        $administrador = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoAdmin, session()->get('usuario_id'));

        if($administrador == null)
        {
            return false;
        }

        //retornamos true, ou seja, o usuário logado é um administrador
        return true;
    }

     /**
     * Método verifica se o usuario logado faz parte do grupo de CLIENTES
     * @return boolean
     */

     private function isCliente() : bool 
     {
         $grupoCliente = 4;
 
         $cliente = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoCliente, session()->get('usuario_id'));
 
         if($cliente == null)
         {
             return false;
         }
 
         //retornamos true, ou seja, o usuário logado é um administrador
         return true;
     }

     /**
      * Método que define as permissões do Usuário logado
      * Usado exclusivamente no método pegaUsuarioDaSessao()
      *
      * @param object $usuario
      * @return object
      */

     private function definePermissoesDoUsuarioLogado(object $usuario) : object 
     {
        //Definimos se o usuário logado é admin
        //Esse atributo será utilizado no método temPermissaoPara() na Entity Usuario
        $usuario->is_admin = $this->isAdmin();

         if($usuario->is_admin == true)
         {

            $usuario->is_cliente = false;

         }else {
            
            $usuario->is_cliente = $this->isCliente();

         }

         if($usuario->is_admin == false && $usuario->is_cliente == false)
         {

            $usuario->permissoes = $this->recuperaPermissoesDoUsuarioLogado();

         }


         return $usuario;



     }

     /**
      * Método que retorna as permissões do usuário logado
      * 
      * @return array
      */

     private function recuperaPermissoesDoUsuarioLogado() : array
     {
        $permissoesDoUsuario = $this->usuarioModel->recuperaPermissoesDoUsuarioLogado( session()->get('usuario_id'));

        return array_column($permissoesDoUsuario, 'permissao');
     }

}