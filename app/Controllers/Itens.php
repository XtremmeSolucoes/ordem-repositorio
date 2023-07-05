<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Item;

class Itens extends BaseController
{
    private $itemModel;

    public function __construct()
    {
        $this->itemModel = new \App\Models\ItemModel();
    }


    public function index()
    {
        $data = [
            'titulo' => 'Listando os Itens',

        ];

        return view('Itens/index', $data);
    }

    public function exibir(int $id = null)
    {
        $item = $this->buscarItemOu404($id);


        $data = [
            'titulo' => 'Detalhes do &nbsp;' . $item->exibeTipo() . ':&nbsp;&nbsp;' . $item->nome,
            'item' => $item,

        ];

        return view('Itens/exibir', $data);
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
}
