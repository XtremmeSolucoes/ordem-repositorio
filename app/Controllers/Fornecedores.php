<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\ValidacoesTrait;
use App\Entities\Fornecedor;

class Fornecedores extends BaseController
{
    use ValidacoesTrait;

    private $fornecedorModel;
    private $fornecedorNotaFiscalModel;


    public function __construct()
    {
       $this->fornecedorModel = new \App\Models\FornecedorModel(); 
       $this->fornecedorNotaFiscalModel = new \App\Models\FornecedorNotaFiscalModel(); 
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

    public function notas(int $id = null)
    {

        $fornecedor = $this->buscarFornecedorOu404($id);
        $fornecedor->notas_fiscais = $this->fornecedorNotaFiscalModel->where('fornecedor_id', $fornecedor->id)->paginate(10);

        if($fornecedor->notas_fiscais != null)
        {
            $fornecedor->pager = $this->fornecedorNotaFiscalModel->pager;
        }

       
        $data = [
            'titulo' => "Gerenciando notas fiscais do Fornecedor " . esc($fornecedor->razao),
            'fornecedor' => $fornecedor,
        ];

        return view('fornecedores/notas_fiscais', $data);
    }

    public function cadastrarNotaFiscal()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        // envio do token do form
        $retorno['token'] = csrf_hash();
        
        $post = $this->request->getPost();

        $valorNota = str_replace([',', '.'], '', $post['valor_nota']);

        if($valorNota < 1)
        {
            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['valor_nota' => 'O valor da Nota deve ser maior que zero!'];

            return $this->response->setJSON($retorno);
        }

        $validacao = service('validation');

        $regras = [
            'valor_nota' => 'required',
            'data_emissao' => 'required',
            'nota_fiscal' => 'uploaded[nota_fiscal]|max_size[nota_fiscal,1024]|ext_in[nota_fiscal,pdf]',
            'descricao_itens' => 'required',
            
        ];

        $mensagens = [   // Errors
            'nota_fiscal' => [
                'uploaded' => 'Por favor escolha uma Nota Fiscal!',
                'max_size' => 'O tamanho maximo do arquivo, permitido é de 1024mb!',
                'ext_in' => 'Os formatos da Nota Fiscal permitidos são, PDF!',

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

        $fornecedor = $this->buscarFornecedorOu404($post['id']);

        $notaFiscal = $this->request->getFile('nota_fiscal');

        $notaFiscalCaminho = $notaFiscal->store('fornecedores/notas_fiscais');

        $notaFiscalCaminho = WRITEPATH . "uploads/fornecedores/notas_fiscais/$notaFiscalCaminho";

        $nota = [
            'fornecedor_id' => $fornecedor->id,
            'nota_fiscal' => $notaFiscal->getName(),
            'descricao_itens' => $post['descricao_itens'],
            'valor_nota' => str_replace(',', '', $post['valor_nota']),
            'data_emissao' => $post['data_emissao'],
        ];

        $this->fornecedorNotaFiscalModel->insert($nota);
        session()->setFlashdata('sucesso', 'Nota Fiscal cadastrada com sucesso!');
        return $this->response->setJSON($retorno);

    }

    public function exibirNota(string $nota = null)
    {
        if($nota === null)
        {
            return redirect()->to(site_url("fornecedores"))->with('atencao', "Não encontramos a nota fiscal $nota");
        }

        $this->exibeArquivo('fornecedores/notas_fiscais', $nota);
    }

    public function removeNota(string $nota_fiscal = null)
    {
        if($this->request->getMethod() === 'post')
        {
            $objetoNota = $this->buscarNotaFiscalOu404($nota_fiscal);

            $this->fornecedorNotaFiscalModel->delete($objetoNota->id);

            $caminhoNotaFiscal = WRITEPATH. "uploads/fornecedores/notas_fiscais/$nota_fiscal";

            if(is_file($caminhoNotaFiscal))
            {
                unlink($caminhoNotaFiscal);
            }

            return redirect()->back()->with("sucesso", "Nota fiscal removida com sucesso!");
        }

        return redirect()->back();
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
     * Método  que recupera o fornecedor
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

     /**
     * Método  que recupera a nota fiscal
     * 
     * @param string $nota_fiscal
     * @return Exceptions|object
     */

     private function buscarNotaFiscalOu404(string $nota_fiscal = null)
     {
         if (!$nota_fiscal || !$objetoNota = $this->fornecedorNotaFiscalModel->where('nota_fiscal', $nota_fiscal)->first()) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos a Nota Fiscal $nota_fiscal");
         }
         return $objetoNota;
     }
}
