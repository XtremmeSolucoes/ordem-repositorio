<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FormaPagamentoSeeder extends Seeder
{
    public function run()
    {
        $formaPagamentoModel = new \App\Models\FormaPagamentoModel();


        $formas = [

            [

                'nome' => 'Boleto Bancário',
                'descricao' => 'Pagamento via Boleto Bancário gerado na GerenciaNet',
                'ativo' => true,

            ],
            [

                'nome' => 'Cortesia',
                'descricao' => 'Forma de Pagamento que não gere valor',
                'ativo' => true,

            ],
            [

                'nome' => 'Cartão de Crédito',
                'descricao' => 'Forma de Pagamento com cartão de crédito, aceitamos diversas bandeiras',
                'ativo' => true,

            ],
            [

                'nome' => 'Cartão de Débito',
                'descricao' => 'Forma de Pagamento com cartão de débito, aceitamos diversas bandeiras',
                'ativo' => true,

            ],
            [

                'nome' => 'PIX',
                'descricao' => 'Forma de Pagamento via PIX',
                'ativo' => true,

            ],
            [

                'nome' => 'Transferência Bancaria',
                'descricao' => 'Forma de Pagamento via transferência bancaria',
                'ativo' => true,

            ],

        ];

        foreach ($formas as $forma) {

            $formaPagamentoModel->skipValidation(true)->protect(false)->insert($forma);
            
        }

        echo "Formas de Pagamentos criadas com sucesso!";

    }
}
