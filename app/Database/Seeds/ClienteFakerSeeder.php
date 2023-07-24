<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClienteFakerSeeder extends Seeder
{
    public function run()
    {
        $clienteModel = new \App\Models\ClienteModel();

        $usuarioModel = new \App\Models\UsuarioModel();

        $grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();

        //Método pra criar instância Faker / Generator
        $faker = \Faker\Factory::create('pt-BR');

        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker)); // Para criar o cpf
        $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker)); // Para criar o telefone

        $criarQuantosClientes = 1000;

        for ($i=0; $i < $criarQuantosClientes; $i++) { 

            //Montamos os dados do cliente
            $nomeGerado = $faker->unique()->name;
            $emailGerado = $faker->unique()->email;
            
            $cliente = [
                'nome' => $nomeGerado,
                'cpf' => $faker->unique()->cpf,
                'telefone' => $faker->unique()->cellphoneNumber,
                'email' => $emailGerado,
                'cep' => $faker->postcode,
                'endereco' => $faker->streetName,
                'numero' => $faker->buildingNumber,
                'bairro' => $faker->city,
                'cidade' => $faker->city,
                'estado' => $faker->stateAbbr,
            ];

            //Criamos o Cliente
            $clienteModel->skipValidation(true)->insert($cliente);

            //Montamos os dados do usuário do cliente
            $usuario = [
               'nome'     => $nomeGerado,
               'email'    => $emailGerado,
               'password' => '123456789',
               'ativo'    => true,
            ];

            //CRIAMOS O USUÁRIO DO CLIENTE
            $usuarioModel->skipValidation(true)->protect(false)->insert($usuario);


            //MONTAMOS OS DADOS DO GRUPO QUE O USUÁRIO FARÁ PARTE
            $grupoUsuario = [
                'grupo_id'   => 4,
                'usuario_id' => $usuarioModel->getInsertID(),
            ];

            //INSERIMOS O USUÁRIO NO GRUPO DE CLIENTES
            $grupoUsuarioModel->protect(false)->insert($grupoUsuario);


            //ATUALIZAMOS A TABELA DE CLIENTES COM O ID DO USUÁRIO CRIADO
            $clienteModel
                        ->protect(false)
                        ->where('id', $clienteModel->getInsertID())
                        ->set('usuario_id', $usuarioModel->getInsertID())
                        ->update();
        } // fim do FOR

        echo "$criarQuantosClientes clientes semeados com sucesso!";

    }
}
