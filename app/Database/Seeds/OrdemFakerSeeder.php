<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OrdemFakerSeeder extends Seeder
{
    public function run()
    {
        $clienteModel = new \App\Models\ClienteModel();
        $ordemModel = new \App\Models\OrdemModel();
        $ordemResponsavelModel = new \App\Models\OrdemResponsavelModel();


        //recupera os IDs dos clientes da base de dados
        $clientes = $clienteModel->select('id')->findAll();
        $clientesIDs = array_column($clientes, 'id');


        //Método pra criar instância Faker / Generator
        $faker = \Faker\Factory::create('pt-BR');

        helper('text');

        for ($i=0; $i < count($clientesIDs); $i++) {

            $ordem = [

                'cliente_id' => $faker->randomElement($clientesIDs),
                'codigo' => $ordemModel->gereCodigoOrdem(),
                'situacao' => 'aberta',
                'equipamento' => $faker->name(),
                'defeito' => $faker->realText(),

            ];

            //inserimos a ordem
            $ordemModel->skipValidation(true)->insert($ordem);

            //iserimos responsavel
            $ordemResponsavel = [
                'ordem_id' => $ordemModel->getInsertID(),
                'usuario_abertura_id' => 52,
            ];

            $ordemResponsavelModel->skipValidation(true)->insert($ordemResponsavel);

        }

        echo count($clientesIDs). 'ordens criadas com sucesso!';
    }
}
