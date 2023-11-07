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

    public function atualizarQuantidade(string $codigo = null)
    {

       if ($this->request->getMethod() !== 'post') {

        return redirect()->back();
        
       }

        $validacao = service('validation');

        $regras = [
            'item_id' => 'required',
            'item_quantidade' => 'required|greater_than[0]',
            'id_principal' => 'required|greater_than[0]', //primary key da tabela ordens_itens
        ];

        $mensagens = [   // Errors
            'item_id' => [
                'required' => 'Não conseguimos identificar qual é o item a ser atualizado!',

            ],
            'item_quantidade' => [
                'required' => 'Por favor escolha uma quantidade maior que zero!',
                'greater_than' => 'Por favor escolha uma quantidade maior que zero!',

            ],
            'id_principal' => [
                'required' => 'Não conseguimos processar a sua requisição!',
                'greater_than' => 'Não conseguimos processar a sua requisição!',

            ],
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() === false) {

            return redirect()->back()->with('atencao', 'Verifique os erros e tente novamente')
                                     ->with('erros_model', $validacao->getErrors());   

        }

        // recuperar o post da requisição
        $post = $this->request->getPost();

        //busco a ordem de serviço 
        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        //Validamos a existência do Item
        $item = $this->buscarItemOu404($post['item_id']);

        //validamos a existencia do registro principal
        $ordemItem = $this->buscarOrdemItemOu404($post['id_principal'], $ordem->id);



        if ($item->tipo === 'produto' && $post['item_quantidade'] > $item->estoque) {

            return redirect()->back()->with('atencao', 'Verifique os erros e tente novamente')
                                     ->with('erros_model', ['estoque' => "Temos apenas <b class='text-white'>$item->estoque</b> em estoque do item $item->nome"]);

        }

        if ($post['item_quantidade'] === $ordemItem->item_quantidade) {

            return redirect()->back()->with('info', 'Informe uma quantidade diferente da existente!');
            
        }

        //Objeto já está alterado com as contidades modificadas
        $ordemItem->item_quantidade = $post['item_quantidade'];

        if ($this->ordemItemModel->atualizarQuantidadeItem($ordemItem)) {

            return redirect()->back()->with('sucesso', 'Quantidade atualizada com sucesso!');
            
        }

        return redirect()->back()->with('atencao', 'Verifique os erros e tente novamente')
                                 ->with('erros_model', $this->ordemItemModel->errors());

        
    }

    public function removerItem(string $codigo = null)
    {

       if ($this->request->getMethod() !== 'post') {

        return redirect()->back();
        
       }

        $validacao = service('validation');

        $regras = [
            'item_id' => 'required',
            'id_principal' => 'required|greater_than[0]', //primary key da tabela ordens_itens
        ];

        $mensagens = [   // Errors
            'item_id' => [
                'required' => 'Não conseguimos identificar qual é o item a ser Excçuído!',

            ],
            'id_principal' => [
                'required' => 'Não conseguimos processar a sua requisição. Escolha o item a ser removido e tente novamente!',
                'greater_than' => 'Não conseguimos processar a sua requisição. Escolha o item a ser removido e tente novamente!',

            ],
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() === false) {

            return redirect()->back()->with('atencao', 'Verifique os erros e tente novamente')
                                     ->with('erros_model', $validacao->getErrors());   

        }

        // recuperar o post da requisição
        $post = $this->request->getPost();

        //busco a ordem de serviço 
        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        //Validamos a existência do Item
        $item = $this->buscarItemOu404($post['item_id']);

        //validamos a existencia do registro principal
        $ordemItem = $this->buscarOrdemItemOu404($post['id_principal'], $ordem->id);

        if ($this->ordemItemModel->delete($ordemItem->id)) {

            return redirect()->back()->with('sucesso', 'Item removido com sucesso!');
            
        }

        return redirect()->back()->with('atencao', 'Verifique os erros e tente novamente')
                                 ->with('erros_model', $this->ordemItemModel->errors());

        
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
     * Método  que recupera o REGISTRO PRINCIPAL
     * 
     * @param interger $id_principal
     * @param interger $ordem_id
     * @return Exceptions|object
     */

     private function buscarOrdemItemOu404(int $id_principal = null, int $ordem_id)
     {
         if (!$id_principal || !$ordemItem = $this->ordemItemModel->where('id', $id_principal)->where('ordem_id', $ordem_id)->first()) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o registro principal $id_principal");
         }
         return $ordemItem;
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
