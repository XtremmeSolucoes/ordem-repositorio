<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\ContaPagar;

class ContasPagar extends BaseController
{
    private $contaPagarModel;
    private $fornecedorModel;
    private $eventoModel;

    public function __construct()
    {
        $this->contaPagarModel = new \App\Models\ContaPagarModel();
        $this->fornecedorModel = new \App\Models\FornecedorModel();
        $this->eventoModel = new \App\Models\EventoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Lista as Contas',
        ];

        return view('ContasPagar/index', $data);
    }

    public function recuperaContas()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $contas = $this->contaPagarModel->recuperaContasPagar();

        //Recebe o array de objetos de Fornecedor
        $data = [];

        foreach ($contas as $conta) {

            $data[] = [
                'razao' => anchor("contas/exibir/$conta->id", esc($conta->razao . ' - CNPJ ' . $conta->cnpj), 'title="Exibir dados da conta ' . esc($conta->razao) . '"'),
                'valor_conta' => 'R$ ' . esc(number_format($conta->valor_conta, 2)),
                'situacao' => $conta->exibeSituacao(),

            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {
        $conta = new ContaPagar();

        $data = [
            'titulo' => "Criando nova Conta",
            'conta'  => $conta,

        ];

        return view('ContasPagar/criar', $data);
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



        $conta = new ContaPagar($post);

        $conta->valor_conta = str_replace(",", "", $conta->valor_conta);

        if ($this->contaPagarModel->save($conta)) {

            if ($conta->situacao == 0) {

                $this->cadastraEventoDaConta($conta);
            }

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            $retorno['id'] = $this->contaPagarModel->getInsertID();
            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->contaPagarModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    /**
     * Método que recupera os forneceores para o selectize
     * @return response 
     */

    public function buscaFornecedores()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'CONCAT(razao, " CNPJ ", cnpj) AS razao',
            'cnpj',
        ];

        $termo = $this->request->getGet('termo');

        $fornecedores = $this->fornecedorModel->select($atributos)
            ->asArray()
            ->like('razao', $termo)
            ->orLike('cnpj', $termo)
            ->where('ativo', true)
            ->orderBy('razao', 'ASC')
            ->findAll();

        return $this->response->setJSON($fornecedores);
    }

    public function exibir(int $id = null)
    {
        $conta = $this->contaPagarModel->buscaContaOu404($id);

        $data = [
            'titulo' => "Listando dados da Conta $conta->razao",
            'conta'  => $conta
        ];

        return view('ContasPagar/exibir', $data);
    }

    public function editar(int $id = null)
    {
        $conta = $this->contaPagarModel->buscaContaOu404($id);

        $data = [
            'titulo' => "Editando dados da Conta $conta->razao",
            'conta'  => $conta
        ];

        return view('ContasPagar/editar', $data);
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

        $conta = $this->contaPagarModel->buscaContaOu404($post['id']);

        $conta->fill($post);

        if ($conta->hasChanged() === false) {
            $retorno['info'] = 'Não há dados para atualizar!';
            return $this->response->setJSON($retorno);
        }

        $conta->valor_conta = str_replace(",", "", $conta->valor_conta);
    

        if ($this->contaPagarModel->save($conta)) {

            if ($conta->hasChanged('data_vencimento') && $conta->situacao == 0) {

                $dias = $conta->defineDataVencimentoEvento();
                
                $this->eventoModel->atualizaEvento('conta_id', $conta->id, $dias);
                
            }

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->contaPagarModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function excluir(int $id = null)
    {
        $conta = $this->contaPagarModel->buscaContaOu404($id);

        if ($this->request->getMethod() === 'post') {

            $this->contaPagarModel->delete($id);

            return redirect()->to(site_url("contas"))->with('sucesso', "Conta do Fornecedor $conta->razao excluída com sucesso!");
        }

        $data = [
            'titulo' => "Excluindo a Conta do Fornecedor " . esc($conta->razao),
            'conta' => $conta,
        ];

        return view('ContasPagar/excluir', $data);
    }

    //-----------------------MÉTODOS PRIVADOS -----------------------------------------//

    private function cadastraEventoDaConta(object $conta) 
    {
        $fornecedor = $this->fornecedorModel->select('razao, cnpj')->find($conta->fornecedor_id);

                $razao = esc($fornecedor->razao);
                $cnpj = esc($fornecedor->cnpj);
                $valorConta = 'R$ ' . esc(number_format($conta->valor_conta, 2));

                $tituloEvento = "Conta do Fornecedor $razao - CNPJ: $cnpj | Valor: $valorConta";

                $dias = $conta->defineDataVencimentoEvento();

                //RECEBE O ID DA CONTA RECÉM CRIADA
                $contaId = $this->contaPagarModel->getInsertID();

                //CADATRAR O EVENTO ATRELADO A CONTA DO FORNECEDOR
                $this->eventoModel->cadastraEvento('conta_id', $tituloEvento, $contaId, $dias);
    }
}
