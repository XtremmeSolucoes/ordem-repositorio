<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Ordem;
use App\Traits\OrdemTrait;

class Ordens extends BaseController
{
    use OrdemTrait;
    
    private $ordemModel;
    private $transacaoModel;

    public function __construct()
    {
        $this->ordemModel = new \App\Models\OrdemModel();
        $this->transacaoModel = new \App\Models\TransacaoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando as ordens de serviços',
        ];

        return view('Ordens/index', $data);
    }

    public function recuperaOrdens()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $ordens = $this->ordemModel->recuperaOrdens();

        //Recebe o array de objetos de Cliente
        $data = [];

        foreach ($ordens as $ordem) {

            $data[] = [
                'codigo' => anchor("ordens/detalhes/$ordem->codigo", esc($ordem->codigo), 'title="Exibir dados da Ordem ' . esc($ordem->codigo) . '"'),
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

    public function detalhes(string $codigo = null)
    {

        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        //Invocando o OrdemTrait
        $this->preparaItensDaOrdem($ordem);

        //Verificar se essa ordem possui uma transação
        $transacao = $this->transacaoModel->where('ordem_id', $ordem->id)->first();

        if ($transacao !== null) {

            $ordem->transação = $transacao;
            
        }

        $data = [
            'titulo' => 'Detalhando a Ordem de Serviço',
            'ordem' => $ordem,
        ];

        return view('Ordens/detalhes', $data);

    }
}
