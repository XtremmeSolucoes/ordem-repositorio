<?php

namespace App\Models;

use CodeIgniter\Model;

class FornecedorNotaFiscalModel extends Model
{
    protected $table            = 'fornecedores_notas_fiscais';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'fornecedor_id',
        'valor_nota',
        'descricao_itens',
        'nota_fiscal',
        'data_emissao',
        
    ];

   //VALIDAÇÃO SERÁ FEITA NO CONTROLE
}
