<?php

namespace App\Controllers;

use App\Controllers\BaseController;

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