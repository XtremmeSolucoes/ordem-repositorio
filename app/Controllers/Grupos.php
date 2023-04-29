<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Grupo;


class Grupos extends BaseController
{
    private $grupoModel;

    public function __construct()
    {
        $this->grupoModel = new \App\Models\GrupoModel();
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
                'exibir' => ($grupo->exibir == true ? '<i class="fa fa-eye text-secondary"></i>&nbsp;Exibir Grupo' : '<i class="fa fa-eye-slash text-danger"></i>&nbsp;Não Exibri Grupo'),
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }
}
