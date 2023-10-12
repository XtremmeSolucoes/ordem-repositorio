<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriaTabelaOrdens extends Migration
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
            'cliente_id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'null'       => true,
            ],
            'codigo' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
            ],
            'forma_pagamento' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null' => true, //será populada na hora do encerramento da ordem
            ],
            'situacao' => [
                'type'       => 'ENUM',
                'constraint' => ['aberta', 'encerrada', 'aguardando', 'cancelada', 'nao_pago'],
                'default' => 'aberta',
            ],
            'itens' => [
                'type'       => 'TEXT', //SERÁ SEREALIZADO
                'null' => true, 
            ],    
            'valor_produtos' => [
                'type'       => 'DECIMAL', //SERÁ SEREALIZADO
                'constraint' => '10,2',
                'null' => true, 
            ],    
            'valor_servicos' => [
                'type'       => 'DECIMAL', //SERÁ SEREALIZADO
                'constraint' => '10,2',
                'null' => true, 
            ],    
            'valor_desconto' => [
                'type'       => 'DECIMAL', //SERÁ SEREALIZADO
                'constraint' => '10,2',
                'null' => true, 
            ],    
            'valor_ordem' => [
                'type'       => 'DECIMAL', //SERÁ SEREALIZADO
                'constraint' => '10,2',
                'null' => true, 
            ],    
            'equipamento' => [
                'type'       => 'VARCHAR', //SERÁ SEREALIZADO
                'constraint' => '150',
            ],    
            'defeito' => [
                'type'       => 'TEXT', //SERÁ SEREALIZADO
                'constraint' => '500',
            ],
            'observacoes' => [
                'type'       => 'VARCHAR', //SERÁ SEREALIZADO
                'constraint' => '250',
            ],    
            'parecer_tecnico' => [
                'type'       => 'TEXT', //SERÁ SEREALIZADO
                'constraint' => '500',
                'null' => true,
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
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ordens');
    }

    public function down()
    {

        $this->forge->dropTable('ordens');
    }
}
