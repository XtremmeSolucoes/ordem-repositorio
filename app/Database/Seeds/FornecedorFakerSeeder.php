<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FornecedorFakerSeeder extends Seeder
{
    public function run()
    {

        $fornecedorModel = new \App\Models\FornecedorModel();

        //Método pra criar instância Faker / Generator
        $faker = \Faker\Factory::create('pt-BR');

        $faker->addProvider(new \Faker\Provider\pt_BR\Company($faker)); // Para criar o cnpj
        $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker)); // Para criar o telefone

        $criarQuantosFornecedores = 1000;

        $fornecedorPush = [];

        for ($i = 0; $i < $criarQuantosFornecedores; $i++) { 
            
            array_push($fornecedorPush, [
                'razao' => $faker->unique()->company,
                'cnpj' => $faker->unique()->cnpj,
                'ie' => $faker->unique()->numberBetween(1000000, 9000000), //Criar sequencias de 7 caracteres no IE
                'telefone' => $faker->unique()->cellphoneNumber,
                'email' => $faker->unique()->email,
                'cep' => $faker->postcode,
                'endereco' => $faker->streetName,
                'numero' => $faker->buildingNumber,
                'bairro' => $faker->city,
                'cidade' => $faker->city,
                'estado' => $faker->stateAbbr,
                'ativo' => $faker->numberBetween(1, 0),// true ou false 
                'criado_em' => $faker->dateTimeBetween('-2 month', '-1 days')->format('H-m-d H:i:s'),
                'atualizado_em' => $faker->dateTimeBetween('-2 month', '-1 days')->format('H-m-d H:i:s'),
            ]);
        }

        $fornecedorModel->skipValidation(true)->insertBatch($fornecedorPush);
    }
}
