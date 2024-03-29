<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\Token;

class UsuarioModel extends Model
{
   
    protected $table            = 'usuarios';
    protected $returnType       = 'App\Entities\Usuario';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'nome',
        'email',
        'password',
        'reset_hash',
        'reset_expira_em',
        'imagem',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'nome'     => 'required|min_length[3]|max_length[120]',
        'email'        => 'required|valid_email|max_length[60]|is_unique[usuarios.email,id,{id}]',
        'password'     => 'required|min_length[8]',
        'password_confirmation' => 'required_with[password]|matches[password]',
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome é obrigatório!',
            'min_length' => 'O campo Nome precisa ter no minimo três caractéres!',
            'max_length' => 'O campo Nome precisa ter no máximo 120 caractéres!',
        ],
        'email' => [
            'required' => 'O campo Email é obrigatório!',
            'valid_email' => 'O campo E-mail precisa de um email válido!',
            'max_length' => 'O campo Email precisa ter no máximo 60 caractéres!', 
            'is_unique' => 'Esse Email já está cadastrado para outro usuário, por favor escolha um email diferente!',
        ],
        'password_confirmation' => [
            'required_with' => 'Por favor confirme a sua senha!',
            'matches' => 'As senhas não são iguais, repita a senha escolhida acima!',
        ],

    ];

    // Callbacks
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data){

        if(isset($data['data']['password'])){

            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']);
            unset($data['data']['password_confirmation']);
        }

        return $data;
    }

    /**
     * Método que recupera o usuário para logar na aplicação
     * @param string $email
     * @return null|object
     */

    public function buscaUsuarioPorEmail(string $email)
    {
        return $this->where('email', $email)->where('deletado_em', null)->first();
    }

    /**
     * Método que recupera as permissões do usuário logado
     * @param integer $usuario_id
     * @return null|array
     */

    public function recuperaPermissoesDoUsuarioLogado(int $usuario_id)
    {
       $atributos = [
        // 'usuarios.id',
        // 'usuarios.nome AS usuario',
        // 'grupos_usuarios.*',
        'permissoes.nome AS permissao',
       ];

       return $this->select($atributos)
                   ->asArray() 
                   ->join('grupos_usuarios', 'grupos_usuarios.usuario_id = usuarios.id')
                   ->join('grupos_permissoes', 'grupos_permissoes.grupo_id = grupos_usuarios.grupo_id')
                   ->join('permissoes', 'permissoes.id = grupos_permissoes.permissao_id')
                   ->where('usuarios.id', $usuario_id)
                   ->groupBy('permissoes.nome')
                   ->findAll();
    }

    /**
     * Método que recupera o usuário de acordo com o hash do token
     * @param string $token
     * @return null|object
     */

    public function buscaUsuarioParaRedifinirSenha(string $token)
    {
        //Instanciando o objeto da class com o tokem como parâmetro
        $token = new Token($token);

        //Recuperando o hash do token
        $tokenHash = $token->getHash();

        //Consultando o usuário com base no hash
        $usuario = $this->where('reset_hash', $tokenHash)
                        ->where('deletado_em', null)
                        ->first();

        //Validando o usuário encontrado
        if($usuario === null)
        {
            return null;
        }

        //Validamos a válidade do token
        if($usuario->reset_expira_em < date('Y-m-d H:i:s'))
        {
            return null;
        }

        //Tudo Ok, válidado com sucesso
        return $usuario;
    }

    /**
     * MÉTODO QUE ATUALIZA O EMAIL DO USUÁRIO DE ACORDO COM O EMAIL DO CLIENTE 
     * @param integer $usuario_id
     * @param string $email
     * @return void
     */
    
    public function atualizaEmailDoCliente(int $usuario_id, string $email)
    {
        return $this->protect(false)
                    ->where('id', $usuario_id)
                    ->set('email', $email)
                    ->update();

    }

      /**
     * MÉTODO RESPONSÁVEL POR RECUPERAR OS TÉCNICOS 
     * @param string $termo
     * @return null|array
     */

    public function recuperaResponsaveisParaOrdem(string $termo = null)
    {

        if ($termo === null) {

            return [];
            
        }

        $atributos = [
            'usuarios.id',
            'usuarios.nome',
        ];

        $responsaveis = $this->select($atributos)
                    ->join('grupos_usuarios', 'grupos_usuarios.usuario_id = usuarios.id')
                    ->join('grupos', 'grupos.id = grupos_usuarios.grupo_id')
                    ->like('usuarios.nome', $termo)
                    ->where('usuarios.ativo', true)
                    ->where('grupos.exibir', true)
                    ->where('grupos.id !=', 4)// bloqueio de usuario clientes para não escolher como responsável
                    ->where('grupos.deletado_em', null)
                    ->where('usuarios.deletado_em', null)
                    ->groupBy('usuarios.nome')
                    ->findAll();

        if ($responsaveis === null) {

            return [];
                        
        } 
        
        return $responsaveis;

    }
}
