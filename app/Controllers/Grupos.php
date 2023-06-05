<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Grupo;


class Grupos extends BaseController
{
    private $grupoModel;
    private $grupoPermissaoModel;
    private $permissaoModel;

    public function __construct()
    {
        $this->grupoModel = new \App\Models\GrupoModel();
        $this->grupoPermissaoModel = new \App\Models\GrupoPermissaoModel();
        $this->permissaoModel = new \App\Models\PermissaoModel();
    }


    public function index()
    {
        $data = [
            'titulo' => 'Lista de Grupos de acesso ao Sistema',
        ];

        return view('Grupos/index', $data);
    }

    public function recuperagrupos()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        $atributos = [
            'id',
            'nome',
            'descricao',
            'exibir',
            'deletado_em'
        ];

        $grupos = $this->grupoModel->select($atributos)
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];

        foreach ($grupos as $grupo) {

            $data[] = [
                'nome' => anchor("grupos/exibir/$grupo->id", esc($grupo->nome), 'title="Exibir dados do grupo ' . esc($grupo->nome) . '"'),
                'descricao' => esc($grupo->descricao),
                'exibir' => ($grupo->exibir == true ? '<i class="fa fa-eye text-secondary"></i>&nbsp;Exibir Grupo' : '<i class="fa fa-eye-slash text-danger"></i>&nbsp;Não Exibir Grupo'),
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {

        $grupo = new Grupo();

        $data = [
            'titulo' => "Criando novo grupo de acesso",
            'grupo' => $grupo,
        ];

        return view('Grupos/criar', $data);
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

        //CRIAR NOVO OBJETO DA ENTIDADE uSUÁRIO 

        $grupo = new Grupo($post);

        if ($this->grupoModel->save($grupo)) {

            $btnCriar = anchor("Grupos/criar", 'Cadastrar novo Grupo de acesso', ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Dados salvos com sucesso!<br> $btnCriar");

            $retorno['id'] = $this->grupoModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->grupoModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {

        $grupo = $this->buscarGrupoOu404($id);

        $data = [
            'titulo' => "Detalhes do grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return view('Grupos/exibir', $data);
    }

    public function editar(int $id = null)
    {

        $grupo = $this->buscarGrupoOu404($id);

        if ($grupo->id < 3) {
            return redirect()->back()->with('atencao', 'Esse Grupo não pode ser editado ou excluido!');
        }

        $data = [
            'titulo' => "Editando o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return view('Grupos/editar', $data);
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

        $grupo = $this->buscarGrupoOu404($post['id']);

        //Proteção contra html inject

        if ($grupo->id < 3) {

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['grupo' => 'O Grupo <b class="text-white">' . esc($grupo->nome) . '</b> não pode ser editado ou excluido!'];
            return $this->response->setJSON($retorno);
        }

        // preencher os atributos do usuário com os valores do Post

        $grupo->fill($post);

        if ($grupo->hasChanged() == false) {

            $retorno['info'] = 'Não existem dados para serem atualizados!';
            return $this->response->setJSON($retorno);
        }

        if ($this->grupoModel->protect(false)->save($grupo)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->grupoModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function excluir(int $id = null)
    {

        $grupo = $this->buscarGrupoOu404($id);

        if ($grupo->id < 3) {
            return redirect()->back()->with('atencao', 'Esse Grupo não pode ser editado ou excluido!');
        }



        if ($this->request->getMethod() === 'post') {

            //Excluir o grupo
            $this->grupoModel->delete($grupo->id);

            return redirect()->to(site_url("grupos"))->with('sucesso', 'Grupo ' . esc($grupo->nome) . ' excluído com sucesso!');
        }

        $data = [
            'titulo' => "Excluindo o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return view('Grupos/excluir', $data);
    }

    public function permissoes(int $id = null)
    {

        $grupo = $this->buscarGrupoOu404($id);

        if ($grupo->id < 3) {
            return redirect()->back()->with('info', 'Esse Grupo não precisa que seja atribuidas permissões!');
        }

        if ($grupo->id > 2) {

            $grupo->permissoes = $this->grupoPermissaoModel->recuperaPermissoesDoGrupo($grupo->id, 5);
            $grupo->pager = $this->grupoPermissaoModel->pager;
        }

        $data = [
            'titulo' => "Gerenciando as permissões do grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        if (!empty($grupo->permissoes)) {

            $permissoesExistentes = array_column($grupo->permissoes, 'permissao_id');

            $data['permissoesDisponiveis'] = $this->permissaoModel->whereNotIn('id', $permissoesExistentes)->findAll();
        } else {

            $data['permissoesDisponiveis'] = $this->permissaoModel->findAll();
        }

        return view('Grupos/permissoes', $data);
    }

    public function salvarPermissoes()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        // envio do token do form
        $retorno['token'] = csrf_hash();

        // recuperar o post da requisição

        $post = $this->request->getPost();


        //validamos a exixtencia do usuário 

        $grupo = $this->buscarGrupoOu404($post['id']);

        if (empty($post['permissao_id'])) {
            //retorno de erros de validação

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['permissao_id' => 'Escolha uma permissão ou mais para salvar!'];

            // retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        //receberá as permissões do POST

        $permissaoPush = [];

        foreach ($post['permissao_id'] as $permissao) {

            array_push($permissaoPush, [
                'grupo_id' => $grupo->id,
                'permissao_id' => $permissao,
            ]);
        }

        $this->grupoPermissaoModel->insertBatch($permissaoPush);

        session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

        return $this->response->setJSON($retorno);


    }

    public function removePermissao(int $principal_id = null)
    {

        if ($this->request->getMethod() === 'post') {

            //Excluir o grupo
            $this->grupoPermissaoModel->delete($principal_id);

            return redirect()->back()->with('sucesso', 'Permissão removida com sucesso!');
        }

        return redirect()->back();
    }

   

    /**
     * Método  que recupera o grupo de acesso
     * 
     * @param interger $id
     * @return Exceptions|object
     */

    private function buscarGrupoOu404(int $id = null)
    {
        if (!$id || !$grupo = $this->grupoModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o grupo de acesso $id");
        }
        return $grupo;
    }
}
