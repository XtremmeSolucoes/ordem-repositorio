<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Item;

class Itens extends BaseController
{
    private $itemModel;
    private $itemHistoricoModel;

    public function __construct()
    {
        $this->itemModel = new \App\Models\ItemModel();
        $this->itemHistoricoModel = new \App\Models\ItemHistoricoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os Itens',

        ];

        return view('Itens/index', $data);
    }

    public function recuperaItens()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'tipo',
            'estoque',
            'preco_venda',
            'ativo',
        ];

        $itens = $this->itemModel->select($atributos)
            ->orderBy('id', 'DESC')
            ->findAll();

        //Recebe o array de objetos de item
        $data = [];

        foreach ($itens as $item) {

            $data[] = [
                'nome' => anchor("itens/exibir/$item->id", esc($item->nome), 'title="Exibir dados do item ' . esc($item->nome) . '"'),
                'tipo' => $item->exibeTipo(),
                'estoque' => $item->exibeEstoque(),
                'preco_venda' => 'R$&nbsp; ' . $item->preco_venda,
                'situacao' => $item->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $item = $this->buscarItemOu404($id);

        $this->defineHistoricoItem($item);

        $data = [
            'titulo' => 'Detalhes do &nbsp;' . $item->exibeTipo() . ':&nbsp;&nbsp;' . $item->nome,
            'item' => $item,

        ];

        return view('Itens/exibir', $data);
    }

    public function criar(int $id = null)
    {
        $item = new Item();


        $data = [
            'titulo' => 'Cadastrando novo Item',
            'item' => $item,

        ];

        return view('Itens/criar', $data);
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

        //validamos a exixtencia do usuário 

        $item = new Item($post);
        $item->codigo_interno = $this->itemModel->gereCodigoInternoItem();

        if ($item->tipo === 'produto') {

            if ($item->marca == "" || $item->marca === null) {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente!';
                $retorno['erros_model'] = ['estoque' => 'Para um <b class="text-white">produto</b>, é necessário informar a Marca'];
                return $this->response->setJSON($retorno);
            }
            if ($item->modelo == "" || $item->modelo === null) {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente!';
                $retorno['erros_model'] = ['estoque' => 'Para um <b class="text-white">produto</b>, é necessário informar o Modelo'];
                return $this->response->setJSON($retorno);
            }
            if ($item->estoque == "") {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente!';
                $retorno['erros_model'] = ['estoque' => 'Para um <b class="text-white">produto</b>, é necessário informar a quantidade em estoque'];
                return $this->response->setJSON($retorno);
            }

            $precoCusto = str_replace([',', '.'], '', $item->preco_custo);
            $precoVenda = str_replace([',', '.'], '', $item->preco_venda);

            if ($precoCusto > $precoVenda) {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente!';
                $retorno['erros_model'] = ['estoque' => 'O <b class="text-white">Preço de Custo</b> não pode ser maior que o preço de venda!'];
                return $this->response->setJSON($retorno);
            }
        }
        
        if ($this->itemModel->save($item)) {
            $btnCriar = anchor("itens/criar", 'Cadastrar nono Item', ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Dados salvos com sucesso!<br> $btnCriar");

            $retorno['id'] = $this->itemModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente!';
        $retorno['erros_model'] = $this->itemModel->errors();
        return $this->response->setJSON($retorno);
    }
    
    public function editar(int $id = null)
    {
        $item = $this->buscarItemOu404($id);


        $data = [
            'titulo' => 'Editando o &nbsp;' . $item->exibeTipo() . ':&nbsp;&nbsp;' . $item->nome,
            'item' => $item,

        ];

        return view('Itens/editar', $data);
    }

    public function codigoBarras(int $id = null)
    {
        // Método para criar barra de codigos do Item
        $item = $this->buscarItemOu404($id);
        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
        $item->codigo_barras =  $generator->getBarcode($item->codigo_interno, $generator::TYPE_CODE_128, 3, 80);

        $data = [
            'titulo' => 'Código de Barras do Item' . $item->exibeTipo(),
            'item' => $item,

        ];

        return view('Itens/codigo_barras', $data);
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

        $item = $this->buscarItemOu404($post['id']);

        $item->fill($post);

        if ($item->hasChanged() === false) {
            $retorno['info'] = 'Não há dados para atualizar!';
            return $this->response->setJSON($retorno);
        }

        if ($item->tipo === 'produto') {
            if ($item->estoque == "") {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente!';
                $retorno['erros_model'] = ['estoque' => 'Para um <b class="text-white">produto</b>, é necessário informar a quantidade em estoque'];
                return $this->response->setJSON($retorno);
            }

            $precoCusto = str_replace([',', '.'], '', $item->preco_custo);
            $precoVenda = str_replace([',', '.'], '', $item->preco_venda);

            if ($precoCusto > $precoVenda) {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente!';
                $retorno['erros_model'] = ['estoque' => 'O <b class="text-white">Preço de Custo</b> não pode ser maior que o preço de venda!'];
                return $this->response->setJSON($retorno);
            }
        }

        if ($this->itemModel->save($item)) {

            $this->insereHistoricoItem($item, 'Atualização');

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente!';
        $retorno['erros_model'] = $this->itemModel->errors();
        return $this->response->setJSON($retorno);
    }


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
     * Método que define o histórico de alterações do item
     * @param object $item
     * @return object
     */

    private function defineHistoricoItem(object $item) : object
    {
        //Método para recuperar o historico do item
        $atributos = [
            'atributos_alterados',
            'criado_em',
            'acao',
        ];

        $historico = $this->itemHistoricoModel
                          ->asArray()
                          ->select($atributos)
                          ->where('item_id', $item->id) 
                          ->orderBy('criado_em', 'DESC')
                          ->findAll();
        if($historico != null){

            foreach ($historico as $key => $hist) {
             
                $historico[$key]['atributos_alterados'] = unserialize($hist['atributos_alterados']);
                
            }

            $item->historico = $historico;

        } 
        
        return $item;
    }

    /**
     * Método que insere o histórico de alterações do item
     * @param object $item
     * @return void
     */

    private function insereHistoricoItem(object $item, string $acao) : void 
    {
        $historico = [
            'usuario_id' => usuario_logado()->id,
            'item_id' => $item->id,
            'acao' => $acao,
            'atributos_alterados' => $item->recuperaAtributosAlterados(),
        ];

        $this->itemHistoricoModel->insert($historico);
    }
}
