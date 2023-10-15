<?php

namespace App\Traits;

/**
 * 
 */

trait OrdemTrait
{

    /**
     * Método que prepara a exibição dos possíveis itens da ordem de serviço
     * 
     * @param object $ordem
     * @return object
     */
    public function preparaItensDaOrdem(object $ordem) : object {

        $ordemItemModel = new \App\Models\OrdemItemModel();

        if ($ordem->situacao === 'aberta') {

            $ordemItens = $ordemItemModel->recuperaItensDaOrdem($ordem->id);
            
            $ordem->itens = (!empty($ordemItens) ? $ordemItens : null);

            return $ordem;

        }

        // A ordem já está diferente de aberta

        if ($ordem->itens !== null) {

            $ordem->itens = unserialize($ordem->itens);
            
        }

        return $ordem;
        
    }

}    