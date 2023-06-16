<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PhpParser\Node\Expr\Cast\Object_;

class Password extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
    }

    public function esqueci()
    {
        $data = [
            'titulo' => 'Esqueci minha senha!',
        ];

        return view('Password/esqueci', $data);
    }

    public function processaEsqueci()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        // envio do hash do form
        $retorno['token'] = csrf_hash();

        // recuperar o E-mail da requisição
        $email = $post = $this->request->getPost('email');

        $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);

        if ($usuario === null || $usuario->ativo === false) {
            $retorno['erro'] = 'Não encontramos uma conta válida com o E-mail informado!';
            return $this->response->setJSON($retorno);
        }

        $usuario->iniciaPasswordReset();

        $this->usuarioModel->save($usuario);

        $this->enviaEmailRedefinicaoSenha($usuario);

        return $this->response->setJSON([]);
    }

    public function resetEnviado()
    {
        $data = [
            'titulo' => 'O E-mail de recuperação da senha foi enviado para sua caixa de entrada!',
        ];

        return view('Password/reset_enviado', $data);
    }

    public function reset($token = null)
    {
        if ($token === null) {
            return redirect()->to(site_url("password/esqueci"))->with("atencao", "Link inválido ou expirado!");
        }

        //Buscar o usuário na base de dados de acordo com o hash do token que veio como parâmetro
        $usuario = $this->usuarioModel->buscaUsuarioParaRedifinirSenha($token);

        if ($usuario === null) {
            return redirect()->to(site_url("password/esqueci"))->with("atencao", "Link inválido ou expirado!");
        }

        $data = [
            'titulo' => 'Digite a sua nova senha!',
            'token'  => $token,
        ];

        return view('Password/reset', $data);
    }

    public function processaReset()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        // envio do hash do form
        $retorno['token'] = csrf_hash();

        // recuperar todos os dados do POST
        $post = $this->request->getPost();

        //Buscar o usuário na base de dados de acordo com o hash do token que veio como parâmetro
        $usuario = $this->usuarioModel->buscaUsuarioParaRedifinirSenha($post['token']);

        if ($usuario === null) {
            //retorno de erros de validação

            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['link_invalido' => 'Link inválido ou expirado!'];

            // retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        $usuario->fill($post);
        
        $usuario->finalizaPasswordReset();

        if ($this->usuarioModel->save($usuario)) {

            session()->setFlashdata("sucesso", "Nova senha criada com sucesso!");

            return $this->response->setJSON($retorno);
        }

        //retorno de erros de validação

        $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        // retorno para o ajax request
        return $this->response->setJSON($retorno);
    }


    /**
     * Método que envia o email para o usuário
     * @param object $usuario
     * @return void
     */

    private function enviaEmailRedefinicaoSenha(object $usuario): void
    {
        $email = service('email');

        $email->setFrom('no-reply@ordem.com', 'Ordem de Serviço');
        $email->setTo($usuario->email);
        $email->setSubject('Redefinição da senha de acesso');

        $data = [
            'token' => $usuario->reset_token,
        ];

        $mensagem = view('Password/reset_email', $data);
        $email->setMessage($mensagem);

        $email->send();
    }
}
