<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GrupoTempSeeder extends Seeder
{
    public function run()
    {
        $grupoModel = new \App\Models\GrupoModel();

        $grupos = [
            [
                'nome' => 'Administrador',
                'descricao' => 'Grupo com acesso total ao sistema',
                'exibir' => false,
            ],
            [
                'nome' => 'Gerente',
                'descricao' => 'Grupo com acesso quase total ao sistema',
                'exibir' => true,
            ],
            [
                'nome' => 'Entregador',
                'descricao' => 'Grupo com acesso as entregas cadastradas no sistema',
                'exibir' => true,
            ],
            [
                'nome' => 'Cliente',
                'descricao' => 'Grupo com acesso de acompanhamento de suas mercadorias',
                'exibir' => true,
            ],
        ];

        foreach($grupos as $grupo){
            $grupoModel->insert($grupo);
        }

        echo "Grupos criados com sucesso";
    }
}
