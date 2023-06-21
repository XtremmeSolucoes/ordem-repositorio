<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\ValidacoesTrait;
use App\Entities\Fornecedor;

class Fornecedores extends BaseController
{
    use ValidacoesTrait;
    private $fornecedorModel;

    public function __construct()
    {
       $this->fornecedorModel = new \App\Models\FornecedorModel(); 
    }


    public function index()
    {
        $data = [
            'titulo' => 'Lista de Fornecedores',
        ];

        return view('Fornecedores/index', $data);
    }

    public function recuperaFornecedores()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        $atributos = [
            'id',
            'razao',
            'cnpj',
            'telefone',
            'ativo',
        ];

        $fornecedores = $this->fornecedorModel->select($atributos)
            ->orderBy('id', 'DESC')
            ->findAll();

        //Recebe o array de objetos de Fornecedor
        $data = [];

        foreach ($fornecedores as $fornecedor) {

            $data[] = [
                'razao' => anchor("fornecedores/exibir/$fornecedor->id", esc($fornecedor->razao), 'title="Exibir dados do fornecedor ' . esc($fornecedor->razao) . '"'),
                'cnpj' => esc($fornecedor->cnpj),
                'telefone' => esc($fornecedor->telefone),
                'ativo' => ($fornecedor->ativo == true ? '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo' : '<i class="fa fa-lock text-danger"></i>&nbsp;Inativo'),
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {

        $fornecedor = new Fornecedor();

        $data = [
            'titulo' => "Cadastrar novo Fornecedor ",
            'fornecedor' => $fornecedor,
        ];

        return view('fornecedores/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        // envio do token do form
        $retorno['token'] = csrf_hash();

        if(session()->get('blockCep') === true)
        {
            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['cep' => 'Informe um CEP válido!'];

            return $this->response->setJSON($retorno);
        }

        // recuperar o post da requisição
        $post = $this->request->getPost();

        $fornecedor = new Fornecedor($post);
        
        if ($this->fornecedorModel->save($fornecedor)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            $retorno['id'] = $this->fornecedorModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->fornecedorModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);


    }

    public function exibir(int $id = null)
    {

        $fornecedor = $this->buscarFornecedorOu404($id);

        $data = [
            'titulo' => "Detalhes do Fornecedor " . esc($fornecedor->nome),
            'fornecedor' => $fornecedor,
        ];

        return view('fornecedores/exibir', $data);
    }

    public function editar(int $id = null)
    {

        $fornecedor = $this->buscarFornecedorOu404($id);

        $data = [
            'titulo' => "Editando o Fornecedor " . esc($fornecedor->razao),
            'fornecedor' => $fornecedor,
        ];

        return view('Fornecedores/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        // envio do token do form
        $retorno['token'] = csrf_hash();

        if(session()->get('blockCep') === true)
        {
            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['cep' => 'Informe um CEP válido!'];

            return $this->response->setJSON($retorno);
        }

        // recuperar o post da requisição
        $post = $this->request->getPost();

        $fornecedor = $this->buscarFornecedorOu404($post['id']);
        $fornecedor->fill($post);

        if($fornecedor->hasChanged() === false)
        {
            $retorno['info'] = 'Não há dados para atualizar!';
            return $this->response->setJSON($retorno);
        }

        if ($this->fornecedorModel->save($fornecedor)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->fornecedorModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);


    }

    public function excluir(int $id = null)
    {
        $fornecedor = $this->buscarFornecedorOu404($id);

        if($this->request->getMethod() === 'post')
        {

            $this->fornecedorModel->delete($id);

            return redirect()->to(site_url("fornecedores"))->with('sucesso', "Fornecedor $fornecedor->razao excluído com sucesso!");
        }

        $data = [
            'titulo' => "Excluindo o Fornecedor " . esc($fornecedor->nome),
            'fornecedor' => $fornecedor,
        ];

        return view('fornecedores/excluir', $data);
    }

    public function consultaCep()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $cep = $this->request->getGet('cep');

        return $this->response->setJSON($this->consultaViaCep($cep));

    }

    /**
     * Método  que recupera o usuário
     * 
     * @param interger $id
     * @return Exceptions|object
     */

     private function buscarFornecedorOu404(int $id = null)
     {
         if (!$id || !$fornecedor = $this->fornecedorModel->withDeleted(true)->find($id)) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o Fornecedor $id");
         }
         return $fornecedor;
     }
}
