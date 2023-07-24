<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Cliente;

class Clientes extends BaseController
{
    private $clienteModel;
    private $usuarioModel;
    private $grupoUsuarioModel;

    public function __construct()
    {
        $this->clienteModel = new \App\Models\ClienteModel();
        $this->usuarioModel = new \App\Models\UsuarioModel();
        $this->grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();
    }
        
    public function index()
    {
        $data = [
            'titulo' => 'Listando os Clientes',
        ];

        return view('Clientes/index', $data);
    }

    public function recuperaClientes()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        $atributos = [
            'id',
            'nome',
            'cpf',
            'email',
            'telefone',
        ];

        $clientes = $this->clienteModel->select($atributos)
            ->orderBy('id', 'DESC')
            ->findAll();

        //Recebe o array de objetos de Cliente
        $data = [];

        foreach ($clientes as $cliente) {

            $data[] = [
                'nome' => anchor("clientes/exibir/$cliente->id", esc($cliente->nome), 'title="Exibir dados do cliente ' . esc($cliente->nome) . '"'),
                'cpf' => esc($cliente->cpf),
                'email' => esc($cliente->email),
                'telefone' => esc($cliente->telefone),               
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $cliente = $this->buscarClienteOu404($id);

        $data = [
            'titulo' => 'Exibindo dados do Clientes'. esc($cliente->nome),
            'cliente' => $cliente,
        ];

        return view('Clientes/exibir', $data);
    }
    
    public function editar(int $id = null)
    {
        $cliente = $this->buscarClienteOu404($id);

        $data = [
            'titulo' => 'Editando dados do Clientes'. esc($cliente->nome),
            'cliente' => $cliente,
        ];

        return view('Clientes/editar', $data);
    }


    /*-------------------------------------Métodos privados------------------------------*/

    /**
     * Método  que recupera o cliente
     * 
     * @param interger $id
     * @return Exceptions|object
     */

     private function buscarClienteOu404(int $id = null)
     {
         if (!$id || !$cliente = $this->clienteModel->withDeleted(true)->find($id)) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o Cliente $id");
         }
         return $cliente;
     }
    
}
