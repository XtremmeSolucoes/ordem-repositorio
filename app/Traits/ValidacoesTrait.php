<?php

namespace App\Traits;

/**
 * 
 */

trait ValidacoesTrait
{
    public function consultaViaCep(string $cep) : array
    {
        //Limpando o CEP
        $cep = str_replace('-', '', $cep);

        $url = "https://viacep.com.br/ws/{$cep}/json/";

        //Abrir a conexão
        $ch = curl_init();

        //difinir URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //executar a consulta
        $resposta = curl_exec($ch);

        //Virificar erros na consulta
        $erro = curl_error($ch);

        $retorno = [];

        if($erro)
        {
            $retorno['erro'] = $erro;
            return $retorno;
        }

        $consulta = json_decode($resposta);

        if(isset($consulta->erro) && !isset($consulta->cep))
        {
            session()->set('blockCep', true); //será usado no controller

            $retorno['erro'] = '<span class="text-danger">Informe um CEP válido!</span>';
            return $retorno;
        }


        session()->set('blockCep', false); //será usado no controller

        $retorno['endereco'] = esc($consulta->logradouro);
        $retorno['bairro']   = esc($consulta->bairro);
        $retorno['cidade']   = esc($consulta->localidade);
        $retorno['estado']   = esc($consulta->uf);

        return $retorno;

    }
}