<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Ordem;
use App\Traits\OrdemTrait;
use Dompdf\Dompdf;

class Ordens extends BaseController
{
    use OrdemTrait;

    private $ordemModel;
    private $transacaoModel;
    private $clienteModel;
    private $ordemResponsavelModel;
    private $usuarioModel;

    public function __construct()
    {
        $this->ordemModel = new \App\Models\OrdemModel();
        $this->transacaoModel = new \App\Models\TransacaoModel();
        $this->clienteModel = new \App\Models\ClienteModel();
        $this->ordemResponsavelModel = new \App\Models\OrdemResponsavelModel();
        $this->usuarioModel = new \App\Models\UsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando as ordens de serviços',
        ];

        return view('Ordens/index', $data);
    }

    public function recuperaOrdens()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $ordens = $this->ordemModel->recuperaOrdens();

        //Recebe o array de objetos de Cliente
        $data = [];

        foreach ($ordens as $ordem) {

            $data[] = [
                'codigo' => anchor("ordens/detalhes/$ordem->codigo", esc($ordem->codigo), 'title="Exibir dados da Ordem ' . esc($ordem->codigo) . '"'),
                'nome' => esc($ordem->nome),
                'cpf' => esc($ordem->cpf),
                'criado_em' => esc($ordem->criado_em->humanize()),
                'situacao' => $ordem->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {
        $ordem = new Ordem();
        $ordem->codigo = $this->ordemModel->gereCodigoOrdem();

        $data = [

            'titulo' => 'Cadastrando nova ordem de serviço',
            'ordem' => $ordem
        ];

        return view('Ordens/criar', $data);
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

        $ordem = new Ordem($post);

        if ($this->ordemModel->save($ordem)) {

            $this->finalizaCadastroDaOrdem($ordem);

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            $retorno['codigo'] = $ordem->codigo;

            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->ordemModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    /**
     * Método que recupera os Clientes para o selectize
     * @return response 
     */

    public function buscaClientes()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'CONCAT(nome, " CPF ", cpf) AS nome',
            'cpf',
        ];

        $termo = $this->request->getGet('termo');

        $clientes = $this->clienteModel->select($atributos)
            ->asArray()
            ->like('nome', $termo)
            ->orLike('cpf', $termo)
            ->orderBy('nome', 'ASC')
            ->findAll();

        return $this->response->setJSON($clientes);
    }

    public function detalhes(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        //Invocando o OrdemTrait
        $this->preparaItensDaOrdem($ordem);

        //Verificar se essa ordem possui uma transação
        $transacao = $this->transacaoModel->where('ordem_id', $ordem->id)->first();

        if ($transacao !== null) {

            $ordem->transação = $transacao;
        }

        $data = [
            'titulo' => 'Detalhando a Ordem de Serviço',
            'ordem' => $ordem,
        ];

        return view('Ordens/detalhes', $data);
    }

    public function editar(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        if ($ordem->situacao === 'encerrada') {

            return redirect()->back()->with("info", "Está Ordem já foi" . ucfirst($ordem->situacao));
        }

        $data = [
            'titulo' => 'Editando a Ordem de Serviço',
            'ordem' => $ordem,
        ];

        return view('Ordens/editar', $data);
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


        //validamos a exixtencia da ordem 

        $ordem = $this->ordemModel->buscaOrdemOu404($post['codigo']);

        if ($ordem->situacao === 'encerrada') {

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['situacao' => "Está Ordem já foi" . ucfirst($ordem->situacao)];

            // retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        // preencher os atributos do usuário com os valores do Post

        $ordem->fill($post);

        if ($ordem->hasChanged() === false) {

            $retorno['info'] = 'Não existem dados para serem atualizados!';
            return $this->response->setJSON($retorno);
        }

        if ($this->ordemModel->save($ordem)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->ordemModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function excluir(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        $situacoesPermitidas = [
            'encerrada',
            'cancelada',
        ];

        if (!in_array($ordem->situacao, $situacoesPermitidas)) {


            return redirect()->back()->with("info", "Apenas ordens encerradas ou canceladas podem ser excluídas");
            
        }

        if ($this->request->getMethod() === 'post') {

            $this->ordemModel->delete($ordem->id);
            return redirect()->to(site_url("ordens"))->with("sucesso", "Ordem $ordem->cogigo excluída com Sucesso!");
            
        }

        $data = [
            'titulo' => "Excluíndo a Ordem de Serviço $ordem->codigo",
            'ordem' => $ordem,
        ];

        return view('Ordens/excluir', $data);
    }

    public function responsavel(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        if ($ordem->situacao === 'encerrada') {

            return redirect()->back()->with("info", "Está Ordem já foi" . ucfirst($ordem->situacao));
        }

        $data = [
            'titulo' => "Defenindo o responsável pela Ordem de Serviço $ordem->codigo",
            'ordem' => $ordem,
        ];

        return view('Ordens/responsavel', $data);
    }

    public function buscaResponsaveis()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }


        $termo = $this->request->getGet('termo');

        $responsaveis = $this->usuarioModel->recuperaResponsaveisParaOrdem($termo);

        return $this->response->setJSON($responsaveis);
    }

    public function definirresponsavel()
    {
        // envio do token do form
        $retorno['token'] = csrf_hash();

        $validacao = service('validation');

        $regras = [
            'usuario_responsavel_id' => 'required|greater_than[0]',
        ];

        $mensagens = [   // Errors
            'usuario_responsavel_id' => [
                'required' => 'Por favor pesquise um responsável técnico e tente novamente!',
                'greater_than' => 'Por favor pesquise um responsável técnico e tente novamente!',

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

        //validamos a exixtencia da ordem 

        $ordem = $this->ordemModel->buscaOrdemOu404($post['codigo']);

        if ($ordem->situacao === 'encerrada') {

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['situacao' => "Está Ordem já foi" . ucfirst($ordem->situacao)];

            // retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        //validamos a exixtência do usuario responsável

        $usuarioResponsavel = $this->buscarUsuarioOu404($post['usuario_responsavel_id']);

        if ($this->ordemResponsavelModel->defineUsuarioResponsavel($ordem->id, $usuarioResponsavel->id)) {

            session()->setFlashdata('sucesso', 'Técnico responsável definido com sucesso!');
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->ordemResponsavelModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function email(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        //Invocando o OrdemTrait
        $this->preparaItensDaOrdem($ordem);

        if ($ordem->situacao === 'aberta') {

            $this->enviaOrdemEmAndamentoParaCliente($ordem);
           
        }else {

            $this->enviaOrdemEncerradaParaCliente($ordem);
            
        }

        return redirect()->to(site_url("ordens/detalhes/$ordem->codigo"))->with('sucesso', 'Ordem Enviada Com sucesso!');

    }

    public function gerarPdf(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        $this->preparaItensDaOrdem($ordem);

        $data = [
            'titulo' => "Gerar PDF da ordem de serviço $ordem->codigo",
            'ordem' => $ordem,
        ];

        //Instanciar o DOMPDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Ordens/gerar_pdf', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("detalhes-da-ordem-$ordem->codigo.pdf", ["Attachment" =>false]);
        
    }


    //------------------------------- Métodos Privados ------------------------------------------//

    private function finalizaCadastroDaOrdem(object $ordem): void
    {
        $ordemAbertura = [
            'ordem_id' => $this->ordemModel->getInsertID(),
            'usuario_abertura_id' => usuario_logado()->id
        ];

        $this->ordemResponsavelModel->insert($ordemAbertura);

        $ordem->cliente = $this->clienteModel->select('nome, email')->find($ordem->cliente_id);

        

        //Será usado na view de email
        $ordem->situacao = 'aberta';
        $ordem->criado_em = date('Y/m/d H:i');

        //Enviando o e-mail para o cliente com o coteúdo da ordem
        $this->enviaOrdemEmAndamentoParaCliente($ordem);
    }


    private function enviaOrdemEncerradaParaCliente(object $ordem) :void 
    {

        $email = service('email');

        $email->setFrom('no-reply@ordem.com', 'Ordem');

        if (isset($ordem->cliente)) {

            $emailCliente = $ordem->cliente->email;
           
        }else {

            $emailCliente = $ordem->email;
            
        }

        $email->setTo($emailCliente);

        if (isset($ordem->transacao)) {

            $tituloEmail = "Ordem de serviço $ordem->codigo encerrada com Boleto Bancário!";
           
        }else {

            $tituloEmail = "Ordem de serviço $ordem->codigo encerrada!";
            
        }

        $email->setSubject($tituloEmail);

        $data = [
            'ordem' => $ordem
        ];

        $mensagem = view('Ordens/ordem_encerrada_email', $data);

        $email->setMessage($mensagem);

        $email->send();
        
    }


    private function enviaOrdemEmAndamentoParaCliente(object $ordem) :void 
    {

        $email = service('email');

        $email->setFrom('no-reply@ordem.com', 'Ordem');

        if (isset($ordem->cliente)) {

            $emailCliente = $ordem->cliente->email;
           
        }else {

            $emailCliente = $ordem->email;
            
        }

        $email->setTo($emailCliente);

        $email->setSubject("Ordem de serviço $ordem->codigo em andamento");

        $data = [
            'ordem' => $ordem
        ];

        $mensagem = view('Ordens/ordem_andamento_email', $data);

        $email->setMessage($mensagem);

        $email->send();
        
    }


    /**
     * Método  que recupera o usuário
     * 
     * @param interger $id
     * @return Exceptions|object
     */

    private function buscarUsuarioOu404(int $usuario_responsavel_id = null)
    {
        if (!$usuario_responsavel_id || !$usuarioResponsavel = $this->usuarioModel->select('id, nome')->where('ativo', true)->find($usuario_responsavel_id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $usuario_responsavel_id");
        }
        return $usuarioResponsavel;
    }
}
