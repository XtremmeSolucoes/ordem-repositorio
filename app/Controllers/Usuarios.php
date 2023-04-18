<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Entities\Usuario;

class Usuarios extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Lista de Usuários do Sistema',
        ];

        return view('Usuarios/index', $data);
    }

    public function recuperaUsuarios()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        $atributos = [
            'id',
            'nome',
            'email',
            'ativo',
            'imagem',
        ];

        $usuarios = $this->usuarioModel->select($atributos)
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];

        foreach ($usuarios as $usuario) {

            $data[] = [
                'imagem' => $usuario->imagem,
                'nome' => anchor("usuarios/exibir/$usuario->id", esc($usuario->nome), 'title="Exibir dados do usuário ' . esc($usuario->nome) . '"'),
                'email' => esc($usuario->email),
                'ativo' => ($usuario->ativo == true ? '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo' : '<i class="fa fa-lock text-danger"></i>&nbsp;Inativo'),
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {

        $usuario = new Usuario();


        $data = [
            'titulo' => "Criando novo usuário",
            'usuario' => $usuario,
        ];

        return view('Usuarios/criar', $data);
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

        $usuario = new Usuario($post);

        if ($this->usuarioModel->protect(false)->save($usuario)) {

            $btnCriar = anchor("usuarios/criar", 'Cadastrar novo Usuário', ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Dados salvos com sucesso!<br> $btnCriar");

            $retorno['id'] = $this->usuarioModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {

        $usuario = $this->buscarUsuarioOu404($id);

        $data = [
            'titulo' => "Detalhes do usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Usuarios/exibir', $data);
    }

    public function editar(int $id = null)
    {

        $usuario = $this->buscarUsuarioOu404($id);

        $data = [
            'titulo' => "Editando o usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Usuarios/editar', $data);
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

        $usuario = $this->buscarUsuarioOu404($post['id']);

        //SE NÃO PREENCHER A SENHA REMOVE DO POST 

        if (empty($post['password'])) {

            unset($post['password']);
            unset($post['password_confirmation']);
        }

        // preencher os atributos do usuário com os valores do Post

        $usuario->fill($post);

        if ($usuario->hasChanged() == false) {

            $retorno['info'] = 'Não existem dados para serem atualizados!';
            return $this->response->setJSON($retorno);
        }

        if ($this->usuarioModel->protect(false)->save($usuario)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function editarImagem(int $id = null)
    {

        $usuario = $this->buscarUsuarioOu404($id);

        $data = [
            'titulo' => "Alterando a imagem do usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Usuarios/editar_imagem', $data);
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
            'imagem' => 'uploaded[imagem]|max_size[imagem,1024]|ext_in[imagem,png,jpg,jpeg,webp]',
        ];

        $mensagens = [   // Errors
            'imagem' => [
                'uploaded' => 'Por favor escolha uma imagem!',
                'max_size' => 'O tamanho maximo da imagem, permitido é de 1024!',
                'ext_in' => 'Os formatos da imagens permitidos são, png, jpg, jpeg ou webp!',

            ],
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() == false) {

            //retorno de erros de validação

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = $validacao->getErrors();

            // retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        exit('tudo certo!');

        // recuperar o post da requisição

        $post = $this->request->getPost();




        //validamos a exixtencia do usuário 

        $usuario = $this->buscarUsuarioOu404($post['id']);

        //SE NÃO PREENCHER A SENHA REMOVE DO POST 

        if (empty($post['password'])) {

            unset($post['password']);
            unset($post['password_confirmation']);
        }

        // preencher os atributos do usuário com os valores do Post

        $usuario->fill($post);

        if ($usuario->hasChanged() == false) {

            $retorno['info'] = 'Não existem dados para serem atualizados!';
            return $this->response->setJSON($retorno);
        }

        if ($this->usuarioModel->protect(false)->save($usuario)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }


    /**
     * Método  que recupera o usuário
     * 
     * @param interger $id
     * @return Exceptions|object
     */

    private function buscarUsuarioOu404(int $id = null)
    {
        if (!$id || !$usuario = $this->usuarioModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");
        }
        return $usuario;
    }
}
