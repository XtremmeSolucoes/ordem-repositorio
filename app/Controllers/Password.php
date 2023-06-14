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
        // envio do token do form
        $retorno['token'] = csrf_hash();

        // recuperar o E-mail da requisição
        $email = $post = $this->request->getPost('email');

        $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);

        if($usuario === null || $usuario->ativo === false)
        {
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

    /**
     * Método que envia o email para o usuário
     * @param object $usuario
     * @return void
     */

    private function enviaEmailRedefinicaoSenha(object $usuario) : void
    {
        $email = service('email');

        $email->setFrom('no-reply@ordem.com', 'Ordem de Serviço');
        $email->setTo($usuario->email);
        $email->setSubject('Redefinição da senha de acesso');
        $email->setMessage('Esmos desenvolvendo, esse é o teste de email de recuperação de senha de acesso do usuário.');
        
        $email->send();
        
    }
}
