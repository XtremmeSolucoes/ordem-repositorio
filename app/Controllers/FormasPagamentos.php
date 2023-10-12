<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\FormaPagamento;

class FormasPagamentos extends BaseController
{
    private $formaPagamentoModel;

    public function __construct()
    {
        $this->formaPagamentoModel = new \App\Models\FormaPagamentoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando as Formas de Pagamentos',
        ];

        return view('FormasPagamentos/index', $data);
    }

    public function recuperaFormas()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $formas = $this->formaPagamentoModel->findAll();

        //Recebe o array de objetos de Fornecedor
        $data = [];

        foreach ($formas as $forma) {

            $data[] = [
                'nome' => anchor("formas/exibir/$forma->id", esc($forma->nome), 'title="Exibir dados da Forma de Pagamento ' . esc($forma->nome) . '"'),
                'descricao' => esc($forma->descricao),
                'criado_em' => esc($forma->criado_em->humanize()),
                'situacao' => $forma->exibeSituacao(),

            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {
        $forma = new FormaPagamento();

        $data = [
            'titulo' => 'Criando nova Forma de Pagamento',
            'forma' => $forma,
        ];

        return view('FormasPagamentos/criar', $data);
    }


    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // envio do token do form
        $retorno['token'] = csrf_hash();

        // recuperar o post da requisição

        $post = $this->request->getPost();

        $forma = new FormaPagamento($post);

        if ($this->formaPagamentoModel->save($forma)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            $retorno['id'] = $this->formaPagamentoModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->formaPagamentoModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);

    }


    public function exibir(int $id = null)
    {
        $forma = $this->buscarFormaOu404($id);

        $data = [
            'titulo' => 'Detalhando a Forma de Pagamento '. esc($forma->nome),
            'forma' => $forma,
        ];

        return view('FormasPagamentos/exibir', $data);
    }

    public function editar(int $id = null)
    {
        $forma = $this->buscarFormaOu404($id);

        if ($forma->id < 3) {

            return redirect()->to(site_url("formas/exibir/$forma->id"))->with("info", "A forma de pagamento <b>$forma->nome</b> não pode ser editada ou excluida!");
          
        }

        $data = [
            'titulo' => 'Editando a Forma de Pagamento '. esc($forma->nome),
            'forma' => $forma,
        ];

        return view('FormasPagamentos/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        // envio do token do form
        $retorno['token'] = csrf_hash();

        // recuperar o post da requisição

        $post = $this->request->getPost();


        //validamos a exixtencia do usuário 

        $forma = $this->buscarFormaOu404($post['id']);

        //Proteção contra html inject

        if ($forma->id < 3) {

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['forma' => 'A Forma de Pagamento <b class="text-white">' . esc($forma->nome) . '</b> não pode ser editada ou excluída!'];
            return $this->response->setJSON($retorno);
        }

        $forma->fill($post);

        if ($forma->hasChanged() === false) {

            $retorno['info'] = 'Não existem dados para serem atualizados!';
            return $this->response->setJSON($retorno);
        }

        if ($this->formaPagamentoModel->save($forma)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->formaPagamentoModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);

    }

    public function excluir(int $id = null)
    {
        $forma = $this->buscarFormaOu404($id);

        if ($forma->id < 3) {

            return redirect()->to(site_url("formas/exibir/$forma->id"))->with("atencao", "A forma de pagamento <b>$forma->nome</b> não pode ser editada ou excluida!");
          
        }

        if ($this->request->getMethod() === 'post') {

            if ($forma->id < 3) {

                return redirect()->to(site_url("formas/exibir/$forma->id"))->with("atencao", "A forma de pagamento <b>$forma->nome</b> não pode ser editada ou excluida!");
              
            }

            $this->formaPagamentoModel->delete($forma->id);
            return redirect()->to(site_url("formas"))->with("sucesso", "A forma de pagamento <b>$forma->nome</b> excluida com sucesso!");


        }

        $data = [
            'titulo' => 'Excluindo a Forma de Pagamento '. esc($forma->nome),
            'forma' => $forma,
        ];

        return view('FormasPagamentos/excluir', $data);
    }

    /*-------------------------------------Métodos privados------------------------------*/

    /**
     * Método  que recupera a Forma de Pagamento
     * 
     * @param interger $id
     * @return Exceptions|object
     */

     private function buscarFormaOu404(int $id = null)
     {
         if (!$id || !$forma = $this->formaPagamentoModel->find($id)) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o Forma de pagamento $id");
         }
         return $forma;
     }


}