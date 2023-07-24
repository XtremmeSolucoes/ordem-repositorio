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
}
