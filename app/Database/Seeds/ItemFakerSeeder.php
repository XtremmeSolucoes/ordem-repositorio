<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ItemFakerSeeder extends Seeder
{
    public function run()
    {
        $itemModel = new \App\Models\ItemModel();

        //Método pra criar instância Faker / Generator
        $faker = \Faker\Factory::create('pt-BR');

        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker)); 


        helper('text');

        $intensPush = [];

        $criarQuantosItens = 5000;

        for ($i = 0; $i < $criarQuantosItens; $i++) { 

            $tipo = $faker->randomElement($array = array('produto', 'serviço'));

            $controlaEstoque = $faker->numberBetween(0, 1);
            
            array_push($intensPush, [
                'codigo_interno'    => $itemModel->gereCodigoInternoItem(),
                'nome'              => $faker->unique()->words(3, true),
                'marca'             => ($tipo === 'produto' ? $faker->word : null),   
                'modelo'            => ($tipo === 'produto' ? $faker->unique()->words(2, true) : null),   
                'preco_custo'       => $faker->randomFloat(2, 10, 100),
                'preco_venda'       => $faker->randomFloat(2, 100, 1000),
                'estoque'           => ($tipo === 'produto' ? $faker->randomDigitNot(0) : null),
                'controla_estoque'  => ($tipo === 'produto' ? $controlaEstoque : null),
                'tipo'              => $tipo,
                'ativo'             => $controlaEstoque,
                'descricao'         => $faker->text(300),
                'criado_em'         => $faker->dateTimeBetween('-2 month', '-1 days')->format('H-m-d H:i:s'),
                'atualizado_em'     => $faker->dateTimeBetween('-2 month', '-1 days')->format('H-m-d H:i:s'),
                
            ]);

        }

        $itemModel->skipValidation(true)->insertBatch($intensPush);

        echo "$criarQuantosItens semeados com sucesso!";


    }    
}
