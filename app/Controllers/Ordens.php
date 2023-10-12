<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Ordem;

class Ordens extends BaseController
{

    private $ordemModel;

    public function __construct()
    {
        $this->ordemModel = new \App\Models\OrdemModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando as ordens de serviços',
        ];

        return view('Ordens/index', $data);
    }

    public function recuperaClientes()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $ordens = $this->ordemModel->recuperaOrdens();

        //Recebe o array de objetos de Cliente
        $data = [];

        foreach ($ordens as $ordem) {

            $data[] = [
                'codigo' => anchor("ordens/exibir/$ordem->codigo", esc($ordem->codigo), 'title="Exibir dados da Ordem ' . esc($ordem->codigo) . '"'),
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
}
