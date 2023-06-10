<?php

namespace App\Controllers;

use App\Libraries\Autenticacao;
use PhpParser\Node\Stmt\Return_;

class Home extends BaseController
{
    public function index()
    {

        $data = [
            'titulo' => 'Home',
        ];

        return view('Home/index', $data);
    }

    public function login()
    {
        $autenticacao = new Autenticacao();

        $autenticacao->login('erik2@gmail.com', '123456789');

        dd($autenticacao->pegaUsuarioLogado());

        //$autenticacao->logout();
        //return redirect()->to(site_url('/'));

        //dd($autenticacao->estaLogado());
    }
}
