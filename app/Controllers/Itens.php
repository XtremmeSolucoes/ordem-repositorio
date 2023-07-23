<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Item;
use PhpParser\Node\Stmt\Foreach_;

class Itens extends BaseController
{
    private $itemModel;
    private $itemHistoricoModel;
    private $itemImagemModel;

    public function __construct()
    {
        $this->itemModel = new \App\Models\ItemModel();
        $this->itemHistoricoModel = new \App\Models\ItemHistoricoModel();
        $this->itemImagemModel = new \App\Models\ItemImagemModel();
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

        if($item->tipo === "produto"){

            $itemImagem = $this->itemImagemModel->select('imagem')->where('item_id', $item->id)->first();

            if($itemImagem !== null){

                $item->imagem = $itemImagem->imagem;

            }

        }

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

    public function editarImagem(int $id = null)
    {
        $item = $this->buscarItemOu404($id);

        if ($item->tipo === 'serviço') {

            return redirect()->back()->with('info', "Só é possível alterar imagem dos itens do tipo produto!");
        }

        $item->imagens = $this->itemImagemModel->where('item_id', $item->id)->findAll();

        $data = [
            'titulo' => 'Gerenciando as imagens do Item &nbsp;' . $item->nome . ' ' . $item->exibeTipo(),
            'item' => $item,

        ];

        return view('Itens/editar_imagem', $data);
    }

    public function upload()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        // envio do token do form
        $retorno['token'] = csrf_hash();

        $validacao = service('validation');

        $regras = [
            'imagens' => 'uploaded[imagens]|max_size[imagens,1024]|ext_in[imagens,png,jpg,jpeg,webp]',
        ];

        $mensagens = [   // Errors
            'imagens' => [
                'uploaded' => 'Por favor escolha uma imagem ou mais imagens!',
                'max_size' => 'O tamanho maximo da imagem, permitido é de 1024!',
                'ext_in' => 'Os formatos da imagens permitidos são, png, jpg, jpeg ou webp!',

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

        //validamos a exixtencia do ITEM 

        $item = $this->buscarItemOu404($post['id']);

        //Limitando a cantidade de imagens por iten
        $resultadoTotalImagens = $this->defineQuantidadeImagens($item->id);

        if($resultadoTotalImagens['totalImagens'] > 5){

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['total_imagens' => "É permitido no máximo 5 imagens por produto. Esse produto já possui ". $resultadoTotalImagens['existentes']. ' Imagens!'];

            // retorno para o ajax request
            return $this->response->setJSON($retorno);

        }


        $imagens = $this->request->getFiles('imagens');

        //FOREACH PARA VALIDAR LARGURA E ALTURA DA IMAGEM

        foreach ($imagens['imagens'] as $imagem) {

            list($largura, $altura) = getimagesize($imagem->getPathName());

            if ($largura < "400" || $altura < "400") {

                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
                $retorno['erros_model'] = ['dimensao' => 'A imagem não pode ser menor do que 400 X 400 pixels!'];

                // retorno para o ajax request
                return $this->response->setJSON($retorno);
            }
        }

        //Receberá as imagens para o insertBatch

        $arrayImagens = [];
        foreach ($imagens['imagens'] as $imagem) {

            $caminhoImagem = $imagem->store('itens');
            $caminhoImagem = WRITEPATH . "uploads/$caminhoImagem";

            $this->manipulaImagem($caminhoImagem, $item->id);

            array_push($arrayImagens, [
                'item_id' => $item->id,
                'imagem'  => $imagem->getName(),
            ]);
            
        } 
        
        $this->itemImagemModel->insertBatch($arrayImagens);
        session()->setFlashdata('sucesso', 'Imagens salvas com sucesso!');
        return $this->response->setJSON($retorno);

    }

    public function imagem(string $imagem = null)
    {
        if($imagem != null){

            $this->exibeArquivo('itens', $imagem);

        }
    }

    public function removeImagem(string $imagem = null)
    {
        if($this->request->getMethod() === 'post')
        {
            $objetoImagem = $this->buscarImagemOu404($imagem);

            $this->itemImagemModel->delete($objetoImagem->id);

            $caminhoImagem = WRITEPATH. "uploads/itens/$imagem";

            if(is_file($caminhoImagem))
            {
                unlink($caminhoImagem);
            }

            return redirect()->back()->with("sucesso", "Imagem removida com sucesso!");
        }

        return redirect()->back();
    }

    public function excluir(int $id = null)
    {
        $item = $this->buscarItemOu404($id);

        if($this->request->getMethod() === 'post')
        {

            $this->itemModel->delete($item->id);

            if($item->tipo === 'produto'){

                $this->removeTodasImagensDoItem($item->id);

            }

            return redirect()->to(site_url("itens"))->with('sucesso', "Item $item->nome excluído com sucesso!");
        }

        $data = [
            'titulo' => 'Excluindo o Item &nbsp;' . $item->nome . ' ' . $item->exibeTipo(),
            'item' => $item,

        ];


        return view('itens/excluir', $data);
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
     * Método  que recupera a IMAGEM
     * 
     * @param string $imagem
     * @return Exceptions|object
     */

    private function buscarImagemOu404(string $imagem = null)
     {
         if (!$imagem || !$objetoImagem = $this->itemImagemModel->where('imagem', $imagem)->first()) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos a imagem $imagem");
         }
         return $objetoImagem;
     }

    /**
     * Método que define o histórico de alterações do item
     * @param object $item
     * @return object
     */

    private function defineHistoricoItem(object $item): object
    {
        //Método para recuperar o historico do item
        

        $historico = $this->itemHistoricoModel->recuperaHistoricoItem($item->id);
            
        if ($historico != null) {

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

    private function insereHistoricoItem(object $item, string $acao): void
    {
        $historico = [
            'usuario_id' => usuario_logado()->id,
            'item_id' => $item->id,
            'acao' => $acao,
            'atributos_alterados' => $item->recuperaAtributosAlterados(),
        ];

        $this->itemHistoricoModel->insert($historico);
    }

    private function manipulaImagem(string $caminhoImagem, int $item_id)
    {
        service('image')
           ->withFile($caminhoImagem)
           ->fit(400, 400, 'center')
           ->save($caminhoImagem);

        $anoAtual = date('Y');   
        
        //Adcionar Marca D'água de texto
        \Config\Services::image('imagick')
           ->withFile($caminhoImagem)
           ->text("Ordem $anoAtual - Produto-ID $item_id", [
                'color'      => '#fff',
                'opacity'    => 0.3,
                'withShadow' => false,
                'hAlign'     => 'center',
                'vAlign'     => 'bottom',
                'fontSize'   => 15
           ])
           ->save($caminhoImagem);
    }

    /**
     * Método que define numero de imagem por produto
     * @param array $item_id
     * @return int
     */

    private function defineQuantidadeImagens(int $item_id) : array
    {
        $existentes = $this->itemImagemModel->where('item_id', $item_id)->countAllResults();
        $quantidadeImagensPost = count(array_filter($_FILES['imagens']['name']));

        $retorno = [
            'existentes' => $existentes,
            'totalImagens' => $existentes + $quantidadeImagensPost,
        ];
        return $retorno;
    }

    /**
     * Método que deleta as imagem de item deletado
     * @param int $item_id
     * @return void
     */

    private function removeTodasImagensDoItem(int $item_id) : void 
    {
        $itemImagens = $this->itemImagemModel->where('item_id', $item_id)->findAll();

        if(empty($itemImagens) === false){

            $this->itemImagemModel->where('item_id', $item_id)->delete();

            foreach ($itemImagens as $imagem) {

                $caminhoImagem = WRITEPATH . "uploads/itens/$imagem->imagem";

                if(is_file($caminhoImagem)){

                    unlink($caminhoImagem);

                }
                
            }

        }

    }


}
