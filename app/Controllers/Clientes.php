<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Traits\ValidacoesTrait;

use App\Entities\Cliente;

class Clientes extends BaseController
{

    use ValidacoesTrait;

    private $clienteModel;
    private $usuarioModel;
    private $grupoUsuarioModel;

    public function __construct()
    {
        $this->clienteModel = new \App\Models\ClienteModel();
        $this->usuarioModel = new \App\Models\UsuarioModel();
        $this->grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os Clientes',
        ];

        return view('Clientes/index', $data);
    }

    public function recuperaClientes()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        $atributos = [
            'id',
            'nome',
            'cpf',
            'email',
            'telefone',
        ];

        $clientes = $this->clienteModel->select($atributos)
            ->orderBy('id', 'DESC')
            ->findAll();

        //Recebe o array de objetos de Cliente
        $data = [];

        foreach ($clientes as $cliente) {

            $data[] = [
                'nome' => anchor("clientes/exibir/$cliente->id", esc($cliente->nome), 'title="Exibir dados do cliente ' . esc($cliente->nome) . '"'),
                'cpf' => esc($cliente->cpf),
                'email' => esc($cliente->email),
                'telefone' => esc($cliente->telefone),
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {
        $cliente = new Cliente();

        $this->removeBlockCepEmailSessao();

        $data = [
            'titulo' => 'Criando novo Cliente',
            'cliente' => $cliente
        ];

        return view('Clientes/criar', $data);
    }

    public function cadastrar(int $id = null)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // envio do token do form
        $retorno['token'] = csrf_hash();

        if (session()->get('blockEmail') === true) {

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['cep' => 'Informe um E-mail válido!'];

            return $this->response->setJSON($retorno);
        }

        if (session()->get('blockCep') === true) {

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['cep' => 'Informe um CEP válido!'];

            return $this->response->setJSON($retorno);
        }

        // recuperar o post da requisição

        $post = $this->request->getPost();

        $cliente = new Cliente($post);

        if ($this->clienteModel->save($cliente)) {


            //cria usuário do cliente
            $this->criaUsuarioParaCliente($cliente);

            //envia ao cliente os dados de acesso criados
            $this->enviaEmailCriacaoEmailAcesso($cliente);

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!<br><br> IMPORTANTE: Um email de notificação foi enviado para o cliente, informando os dados de acesso ao sistema!: <p>E-mail: '.$cliente->email.'</p><p>Senha Inicial: 123456789</p>');
            $retorno['id'] = $this->clienteModel->getInsertID();
            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->clienteModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $cliente = $this->buscarClienteOu404($id);

        $data = [
            'titulo' => 'Exibindo dados do Clientes' . esc($cliente->nome),
            'cliente' => $cliente
        ];

        return view('Clientes/exibir', $data);
    }

    public function editar(int $id = null)
    {
        $cliente = $this->buscarClienteOu404($id);

        $this->removeBlockCepEmailSessao();

        $data = [
            'titulo' => 'Editando dados do Clientes' . esc($cliente->nome),
            'cliente' => $cliente
        ];

        return view('Clientes/editar', $data);
    }


    public function atualizar(int $id = null)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // envio do token do form
        $retorno['token'] = csrf_hash();

        // recuperar o post da requisição

        $post = $this->request->getPost();

        $cliente = $this->buscarClienteOu404($post['id']);

        if (session()->get('blockEmail') === true) {

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['cep' => 'Informe um E-mail válido!'];

            return $this->response->setJSON($retorno);
        }

        if (session()->get('blockCep') === true) {

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['cep' => 'Informe um CEP válido!'];

            return $this->response->setJSON($retorno);
        }

        $cliente->fill($post);

        
        if ($cliente->hasChanged() === false) {
            $retorno['info'] = 'Não há dados para atualizar!';
            return $this->response->setJSON($retorno);
        }

        if ($this->clienteModel->save($cliente)) {

            if ($cliente->hasChanged('email')) {

                $this->usuarioModel->atualizaEmailDoCliente($cliente->usuario_id, $cliente->email);

                
                $this->enviaEmailAlteracaoEmailAcesso($cliente);

                 session()->setFlashdata('sucesso', 'Dados salvos com sucesso!<br><br> IMPORTANTE: Um email de notificação foi enviado para o cliente, informando a alteração no email de acesso ao sistema!');
                 return $this->response->setJSON($retorno);
            }


            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->clienteModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }


    public function consultaCep()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $cep = $this->request->getGet('cep');

        return $this->response->setJSON($this->consultaViaCep($cep));
    }


    public function consultaEmail()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $email = $this->request->getGet('email');

        return $this->response->setJSON($this->checkEmail($email));
    }

    public function excluir(int $id = null)
    {
        $cliente = $this->buscarClienteOu404($id);

        if($this->request->getMethod() === 'post')
        {

            $this->clienteModel->delete($id);

            return redirect()->to(site_url("clientes"))->with('sucesso', "Cliente $cliente->nome excluído com sucesso!");
        }

        $data = [
            'titulo' => "Excluindo o Cliente " . esc($cliente->nome),
            'cliente' => $cliente,
        ];

        return view('Clientes/excluir', $data);
    }


    /*-------------------------------------Métodos privados------------------------------*/

    /**
     * Método  que recupera o cliente
     * 
     * @param interger $id
     * @return Exceptions|object
     */

    private function buscarClienteOu404(int $id = null)
    {
        if (!$id || !$cliente = $this->clienteModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o Cliente $id");
        }
        return $cliente;
    }


    /**
     * Método que envia o email para o cliente informando a criação do email de acesso
     * @param object $cliente
     * @return void
     */

     private function enviaEmailCriacaoEmailAcesso(object $cliente): void
     {
         $email = service('email');
 
         $email->setFrom('no-reply@ordem.com', 'Ordem de Serviço');
         $email->setTo($cliente->email);
         $email->setSubject('Dados de acesso ao sistema');
 
         $data = [
             'cliente' => $cliente,
         ];
 
         $mensagem = view('Clientes/email_dados_acesso', $data);
         $email->setMessage($mensagem);
 
         $email->send();
     }

    /**
     * Método que envia o email para o cliente informando a alteração do email de acesso
     * @param object $cliente
     * @return void
     */

     private function enviaEmailAlteracaoEmailAcesso(object $cliente): void
     {
         $email = service('email');
 
         $email->setFrom('no-reply@ordem.com', 'Ordem de Serviço');
         $email->setTo($cliente->email);
         $email->setSubject('Redefinição de E-mail de acesso');
 
         $data = [
             'cliente' => $cliente,
         ];
 
         $mensagem = view('Clientes/email_acesso_alterado', $data);
         $email->setMessage($mensagem);
 
         $email->send();
     }

     /**
      * Método que remove da sessão o BlockCep e BlockEmail
      *
      * @return void
      */

      private function removeBlockCepEmailSessao() : void 
      {
        session()->remove('blockCep');
        session()->remove('blockEmail');
      }

      /**
       * Método que cria o usuário para o cliente recém cadastrado
       * @param object $cliente
       * @return void
       */

      private function criaUsuarioParaCliente(object $cliente) : void 
      {
        //Montamos os dados do usuário do cliente
        $usuario = [
            'nome'     => $cliente->nome,
            'email'    => $cliente->email,
            'password' => '123456789',
            'ativo'    => true,
         ];

         //CRIAMOS O USUÁRIO DO CLIENTE
         $this->usuarioModel->skipValidation(true)->protect(false)->insert($usuario);


         //MONTAMOS OS DADOS DO GRUPO QUE O USUÁRIO FARÁ PARTE
         $grupoUsuario = [
             'grupo_id'   => 4,
             'usuario_id' => $this->usuarioModel->getInsertID(),
         ];

         //INSERIMOS O USUÁRIO NO GRUPO DE CLIENTES
         $this->grupoUsuarioModel->protect(false)->insert($grupoUsuario);


         //ATUALIZAMOS A TABELA DE CLIENTES COM O ID DO USUÁRIO CRIADO
         $this->clienteModel
                     ->protect(false)
                     ->where('id', $this->clienteModel->getInsertID())
                     ->set('usuario_id', $this->usuarioModel->getInsertID())
                     ->update();
      }
}
