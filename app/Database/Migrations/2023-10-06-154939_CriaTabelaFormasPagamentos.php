<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriaTabelaFormasPagamentos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '128',
                'unique' => true,
            ],
            'descricao' => [
                'type' => 'VARCHAR',
                'constraint' => '240',
            ],
            'ativo' => [ 
                'type' => 'BOOLEAN',
                'null' => false,
            ],
            'criado_em' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'atualizado_em' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'deletado_em' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('formas_pagamentos');
    }

    public function down()
    {

        $this->forge->dropTable('formas_pagamentos');
    }
}
