<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Login extends BaseController
{
    public function novo()
    {
        $data = [
            'titulo' => 'Realize o Login!',
        ];

        return view('Login/novo', $data);
    }

    public function criar() 
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        // envio do token do form
        $retorno['token'] = csrf_hash();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        //recuperando a instancia do serviço autenticação
        $autenticacao = service('autenticacao');


        if($autenticacao->login($email, $password) === false)
        {
            //retorno de erros de validação
            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['credenciais' => 'Credenciais de acesso não encontradas'];
            return $this->response->setJSON($retorno);

        }

            //retorno de sucesso de validação
            $usuarioLogado = $autenticacao->pegaUsuarioLogado();


            session()->setFlashdata('sucesso', "Olá $usuarioLogado->nome, que bom que está de volta!");

            if($usuarioLogado->is_cliente)
            {
                $retorno['redirect'] = 'ordens/minhas';
                return $this->response->setJSON($retorno);
            }

            //Método para usuários normal
            $retorno['redirect'] = 'home';
            return $this->response->setJSON($retorno);    

    }

    public function logout()
    {
        $autenticacao = service('autenticacao');

        $usuarioLogado = $autenticacao->pegaUsuarioLogado();

        $autenticacao->logout();

        return redirect()->to(site_url("login/mostramensagemlogout/$usuarioLogado->nome"));

    }

    public function mostraMensagemLogout($nome = null)
    {
        return redirect()->to(site_url("login"))->with("sucesso", "$nome, esperamos ver você novamente!");
    }

    
}
