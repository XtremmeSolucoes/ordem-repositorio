<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Entities\Usuario;

class Usuarios extends BaseController
{
    private $usuarioModel;
    private $grupoUsuarioModel;
    private $grupoModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
        $this->grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();
        $this->grupoModel = new \App\Models\GrupoModel();
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

            //Definimos o caminho da imagem do usuário

            if($usuario->imagem != null){

                //tem imagem

                $imagem = [
                    'src' => site_url("usuarios/imagem/$usuario->imagem"),
                    'class' => 'rounded-circle img-fluid',
                    'alt' => esc($usuario->nome),
                    'width' => '50',
                ];
            }else {

                //não tem imagem
                 
                $imagem = [
                    'src' => site_url("recursos/img/usuario_sem_imagem.png"),
                    'class' => 'rounded-circle img-fluid',
                    'alt' => 'Usuário sem Imagem',
                    'width' => '50',
                ];
            }

            $data[] = [
                'imagem' => $usuario->imagem = img($imagem),
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

        if ($usuario->hasChanged() === false) {

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

        if ($validacao->withRequest($this->request)->run() === false) {

            //retorno de erros de validação

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = $validacao->getErrors();

            // retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        

      

        // recuperar o post da requisição

        $post = $this->request->getPost();

        //validamos a exixtencia do usuário 

        $usuario = $this->buscarUsuarioOu404($post['id']);

        //recupera a imagem que veio no post 

        $imagem = $this->request->getFile('imagem');

        list($largura, $altura) = getimagesize($imagem->getPathName());

        if($largura < "300" || $altura < "300"){

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['dimensao' => 'A imagem não pode ser menor do que 300 X 300 pixels!'];

            // retorno para o ajax request
            return $this->response->setJSON($retorno);

        }

        $caminhoImagem = $imagem->store('usuarios');
        $caminhoImagem = WRITEPATH . "uploads/$caminhoImagem";

        //Podemos manipular a imagem que está salva no diretório
        //Redimissionamento de imagem para 300X300 centro
        
        $this->manipulaImagem($caminhoImagem, $usuario->id);

        //A parti daqui podemos atualizar a tabela de usuários

        //recuperar imagem antiga se existir
        $imagemAntiga =  $usuario->imagem;    

        $usuario->imagem = $imagem->getName();

        $this->usuarioModel->save($usuario);

        if($imagemAntiga != null){

            $this->removeImagemDoFileSystem($imagemAntiga);

        }

        session()->setFlashdata('sucesso', 'Imagem atualizada com sucesso!');
        
        

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function imagem(string $imagem = null)
    {
        if($imagem != null){

            $this->exibeArquivo('usuarios', $imagem);

        }
    }

    public function excluir(int $id = null)
    {

        $usuario = $this->buscarUsuarioOu404($id);

        if($this->request->getMethod() === 'post'){

            //Excluir o usuário
            $this->usuarioModel->delete($usuario->id);

            //Deletamos a imagem do fileSystem
            if($usuario->imagem != null){

                $this->removeImagemDoFileSystem($usuario->imagem);

            }

            

            return redirect()->to(site_url("usuarios"))->with('sucesso', "Usuário $usuario->nome excluído com sucesso!");

        }

        $data = [
            'titulo' => "Excluindo o usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Usuarios/excluir', $data);
    }

    public function grupos(int $id = null)
    {

        $usuario = $this->buscarUsuarioOu404($id);

        $usuario->grupos = $this->grupoUsuarioModel->recuperaGruposDoUsuario($usuario->id, 5);
        $usuario->pager = $this->grupoUsuarioModel->pager;

        $data = [
            'titulo' => "Gerenciando os grupos de acesso do usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        //limitando o grupo de acesso do cliente
        $grupoCliente = 4;
        if(in_array($grupoCliente, array_column($usuario->grupos, 'grupo_id'))){

            return redirect()->to(site_url("usuarios/exibir/$usuario->id"))
                             ->with('info', "Não é permitido atribuir grupos de acesso para um Cliente");
        }

        $grupoAdmin = 1;
        if(in_array($grupoAdmin, array_column($usuario->grupos, 'grupo_id'))){

            $usuario->full_control  = true; //está no grupo de Administrador
            return view('Usuarios/grupos', $data);
        }

        $usuario->full_control  = false; // não está no grupo de Administrador

        if(!empty($usuario->grupos)){

            //recuperar os grupos que ainda não estão atribuidos ao usuario
            $gruposExistentes = array_column($usuario->grupos, 'grupo_id');
            $data['gruposDisponiveis'] = $this->grupoModel
                                              ->where('id !=', 4) // Não receramos o grupo de clientes
                                              ->whereNotIn('id', $gruposExistentes)
                                              ->findAll();

        }else{

            //recupera todos os grupos permitidos
            $data['gruposDisponiveis'] = $this->grupoModel
                                              ->where('id !=', 4) // Não receramos o grupo de clientes
                                              ->findAll();

        }

        return view('Usuarios/grupos', $data);
    }

    public function salvarGrupos()
    {
         // envio do token do form
         $retorno['token'] = csrf_hash();

         // recuperar o post da requisição
 
         $post = $this->request->getPost();
 
 
         //validamos a exixtencia do usuário 
 
         $usuario = $this->buscarUsuarioOu404($post['id']);

         if (empty($post['grupo_id'])) {

            //retorno de erros de validação
            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['grupo_id' => 'Escolha um grupo ou mais para salvar!'];

            // retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        if(in_array(4, $post['grupo_id'])){
            //retorno de erros de validação
            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['grupo_id' => 'O grupo Cliente não pode ser atribuído manualmente!'];

            // retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        if(in_array(1, $post['grupo_id'])){

            $grupoAdmin = [
                'grupo_id' => 1,
                'usuario_id' => $usuario->id,
            ];

            $this->grupoUsuarioModel->insert($grupoAdmin);
            $this->grupoUsuarioModel->where('grupo_id !=', 1)
                                    ->where('usuario_id', $usuario->id)
                                    ->delete();

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            session()->setFlashdata('info', 'O grupo Administrador foi selecionado para esse usuário, não é necessario selecionar outro grupo!');

            return $this->response->setJSON($retorno);

        }


        //receberá as permissões do POST

        $grupoPush = [];

        foreach ($post['grupo_id'] as $grupo) {

            array_push($grupoPush, [
                'grupo_id' => $grupo,
                'usuario_id' => $usuario->id,
            ]);
        }

        $this->grupoUsuarioModel->insertBatch($grupoPush);

        session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

        return $this->response->setJSON($retorno);


    }

    public function removeGrupo(int $principal_id = null)
    {

        if($this->request->getMethod() === 'post'){

            $grupoUsuario = $this->buscaGrupoUsuarioOu404($principal_id);

            if($grupoUsuario->grupo_id == 4){

                return redirect()->to(site_url("usuarios/exibir/$grupoUsuario->usuario_id"))->with("info", "Não é permitido a exclusão do usuário do grupo de Clientes!");

            }

            $this->grupoUsuarioModel->delete($principal_id);
            return redirect()->back()->with("sucesso", "Usuário removido do grupo de acesso com sucesso!");

        }

        return redirect()->back();

    }

    public function editarSenha()
    {
        //Não colocamos ACL aqui
        $data = [
            'titulo' => 'Editar senha',
        ];

        return view('Usuarios/editar_senha', $data);

    }

    public function atualizarsenha()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        // envio do token do form
        $retorno['token'] = csrf_hash();

        $current_password = $this->request->getPost('current_password');


        //Recuperamos o usuário logado
        $usuario = usuario_logado();

        if($usuario->verificaPassword($current_password) === false)
        {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['current_password' => 'A senha atual informada é inválida!'];
            return $this->response->setJSON($retorno);
        }

        $usuario->fill($this->request->getPost());


        if ($usuario->hasChanged() === false) {

            $retorno['info'] = 'Não existem dados para serem atualizados!';
            return $this->response->setJSON($retorno);
        }

        if ($this->usuarioModel->save($usuario)) {

            $retorno['sucesso'] = 'Senha alterada com sucesso!';

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

    /**
     * Método que recupera o registro do grupo associado ao usuário
     * @param integer $principal_id
     * @return Exception|object
     */

    private function buscaGrupoUsuarioOu404(int $principal_id = null)
    {
        if (!$principal_id || !$grupoUsuario = $this->grupoUsuarioModel->find($principal_id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o registro de associação ao grupo de acesso $principal_id");
        }

        return $grupoUsuario;
        
    }

    private function manipulaImagem(string $caminhoImagem, int $usuario_id)
    {
        service('image')
           ->withFile($caminhoImagem)
           ->fit(300, 300, 'center')
           ->save($caminhoImagem);

        $anoAtual = date('Y');   
        
        //Adcionar Marca D'água de texto
        \Config\Services::image('imagick')
           ->withFile($caminhoImagem)
           ->text("Ordem $anoAtual - User-ID $usuario_id", [
                'color'      => '#fff',
                'opacity'    => 0.3,
                'withShadow' => false,
                'hAlign'     => 'center',
                'vAlign'     => 'bottom',
                'fontSize'   => 15
           ])
           ->save($caminhoImagem);
    }

    private function removeImagemDoFileSystem(string $imagem)
    {

        $caminhoImagem = WRITEPATH . "uploads/usuarios/$imagem";

        if(is_file($caminhoImagem)){

            unlink($caminhoImagem);

        }
    }
}
