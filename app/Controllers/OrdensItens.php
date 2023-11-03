<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\OrdemTrait;

class OrdensItens extends BaseController
{
    use OrdemTrait;

    private $ordemModel;
    private $ordemItemModel;
    private $itemModel;

    public function __construct()
    {
        $this->ordemModel = new \App\Models\OrdemModel();
        $this->ordemItemModel = new \App\Models\OrdemItemModel();
        $this->itemModel = new \App\Models\ItemModel();
    }

    public function itens(string $codigo = null)
    {
        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        //Preparar a exibição dos possíveis itens da ordem
        $this->preparaItensDaOrdem($ordem);

        $data = [
            'titulo' => "Gerenciando os Itens da ordem: $ordem->codigo",
            'ordem' => $ordem,
        ];

        return view('Ordens/itens', $data);
    }

    public function pesquisaItens()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $term = $this->request->getGet('term');

        $itens = $this->itemModel->pesquisaItens($term);

        $retorno = [];

        foreach ($itens as $item) {

            $data['id'] = $item->id;
            $data['item_preco'] = number_format($item->preco_venda, 2);

            $itemTipo = ucfirst($item->tipo);

            if ($item->tipo === 'produto') {

                if ($item->imagem != null) {

                    // Tem imagem
                    $caminhoImagem = "itens/imagem/$item->imagem";
                    $altImagem = $item->nome;

                } else {

                    //NÃO TEM IMAGEM
                    $caminhoImagem = "recursos/img/item_sem_imagem.png";
                    $altImagem = "$item->nome não possui imagem!";
                }

                $data['value'] = "[ Código $item->codigo_interno] [$itemTipo] [ Estoque $item->estoque] $item->nome ";

            } else {

                $caminhoImagem = "recursos/img/imagem_servico.jpg";
                $altImagem = "$item->nome";

                $data['value'] = "[ Código $item->codigo_interno] [$itemTipo] $item->nome ";
            }

            $imagem = [
                'src' => $caminhoImagem,
                'class' => 'img-fluid img-thumbnail',
                'alt' => $altImagem,
                'width' => '50',
            ];

            $data['label'] = '<span>' . img($imagem) . ' ' . $data['value'] . '</span>';

            $retorno[] = $data;
        }

        return $this->response->setJSON($retorno);
    }

    public function adicionarItem()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // envio do token do form
        $retorno['token'] = csrf_hash();

        $validacao = service('validation');

        $regras = [
            'item_id' => 'required',
            'item_quantidade' => 'required|greater_than[0]',
        ];

        $mensagens = [   // Errors
            'item_id' => [
                'required' => 'Por favor pesquise e selecione um item e tente novamente!',

            ],
            'item_quantidade' => [
                'required' => 'Por favor pesquise um item e escolha uma quantidade maior que zero!',
                'greater_than' => 'Por favor pesquise um item e escolha uma quantidade maior que zero!',

            ],
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() === false) {

            //retorno de erros de validação

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = $validacao->getErrors();

            // retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        // recuperar o post da requisição
        $post = $this->request->getPost();

        $ordem = $this->ordemModel->buscaOrdemOu404($post['codigo']);

        //Validamos a existência do Item
        $item = $this->buscarItemOu404($post['item_id']);

        if ($item->tipo === 'produto' && $post['item_quantidade'] > $item->estoque) {

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['estoque' => "Temos apenas <b class='text-white'>$item->estoque</b> em estoque do item $item->nome"];

            // retorno para o ajax request
            return $this->response->setJSON($retorno);

        }

        //exit('Validado');

        //Verificamos se a ordem já possui o item selecionado no modal
        if ($this->verificaSeTemItem($ordem->id, $item->id)) {

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['estoque' => "Essa ordem já possui o item <b class='text-white'>$item->nome</b> cadastrado!"];

            // retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        $ordemItem = [
            'ordem_id' => (int) $ordem->id,
            'item_id' => (int) $item->id,
            'item_quantidade' => (int) $post['item_quantidade'],
        ];

      

        if ($this->ordemItemModel->insert($ordemItem)) {

            session()->setFlashdata('sucesso', "$item->nome adicionado com sucesso!");
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->ordemItemModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
        
    }


    /*-----------------------Métodos privados---------------------------------*/


    /**
     * Método  que recupera o ITEM
     * 
     * @param interger $id
     * @return Exceptions|object
     */

    private function buscarItemOu404(int $id = null)
    {
        if (!$id || !$item = $this->itemModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o item $id");
        }
        return $item;
    }

    /**
     * Método resposável por verificar se a ordem já possui o Item
     *@param integer $ordem_id
     *@param integer $item_id
     *@return boolean
     */

    private function verificaSeTemItem(int $ordem_id, int $item_id) : bool
    {

        $possuiItem = $this->ordemItemModel->where('ordem_id', $ordem_id)->where('item_id', $item_id)->first();

        if ($possuiItem === null) {

            return false;
        }

        //Ordem já possui o item
        return true;
    }
}
