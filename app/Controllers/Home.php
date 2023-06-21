<?php

namespace App\Controllers;

use App\Libraries\Autenticacao;
use App\Traits\ValidacoesTrait;

class Home extends BaseController
{
    use ValidacoesTrait;

    public function index()
    {

        $data = [
            'titulo' => 'Home',
        ];

        return view('Home/index', $data);
    }

    public function login()
    {
        $autenticacao = service('autenticacao');

        $autenticacao->login('erik2@gmail.com', '123456789');

        

        //$autenticacao->logout();
        //return redirect()->to(site_url('/'));

        //dd($autenticacao->estaLogado());
    }

    public function email()
    {
        $email = service('email');

        $email->setFrom('no-reply@ordem.com', 'Ordem de Serviço');
        $email->setTo('adrianofertimbeta@gmail.com');
        $email->setSubject('Teste de recuperação de senha');
        $email->setMessage('Esmos desenvolvendo, esse é o teste de email de recuperação de senha de acesso do usuário.');
        if($email->send())
        {
          echo 'E-mail enviado!';

        }else{

            echo $email->printDebugger();
            
        }
        
    }

    public function cep()
    {
        $cep = "59114-250";

        return $this->response->setJSON($this->consultaViaCep($cep));
    }
}
