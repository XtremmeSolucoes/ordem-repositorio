<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdemResponsavelModel extends Model
{
    protected $table            = 'ordens_responsaveis';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'ordem_id',
        'usuario_abertura_id',
        'usuario_responsavel_id',
        'usuario_encerramento_id',
    ];

    /**
     * Método responsável por definir o técnico responsável pela ordem de serviço
     * @param int $ordem_id
     * @param int $usuario_responsavel_id
     * @return void
     */

    public function defineUsuarioResponsavel(int $ordem_id, int $usuario_responsavel_id)
    {
        return $this->set('usuario_responsavel_id', $usuario_responsavel_id)
                    ->where('ordem_id', $ordem_id)
                    ->update();
    }
}
