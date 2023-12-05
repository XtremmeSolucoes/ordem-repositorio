<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <title>Relatório</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <style type="text/css">
            @page{
                margin-top: 3cm;
                margin-left: 3cm;
                margin-bottom: 2cm;
                margin-left: 2cm;
            }
            #titulo{
                font-size: 14px;
                text-align: center;
            }
            #tabela{
                width: 90%;
                font-size: 12px;
                margin: 0 auto;
                text-align: justify;
                border: 1px solid black;
                border-collapse: collapse;
                white-space: pre-line;
                table-layout: auto;
                width: content-box;
				height: content-box;
            }
            .celula{
                border: 1px solid black;
                text-align: justify;
                font-size: 12px;
            }
            .celula_center{
                border: 1px solid black;
                text-align: center;
                font-size: 12px;
			}
            .celula_head{
                border: 2px solid black;
                background-color: #D3D3D3;
                text-align: center;
                font-size: 12px;
            }
            #rodape-final{
                width: 100%;
                position: absolute;
                top: 22cm;
                left: 0cm;
                bottom: 3cm;
                right: 2cm;
                font-size: 12px;
            }
            #align-right{
                text-align: right;
            }

            .color {
                color: #000019;
            }
        </style>
    </head>
    <body>
        <div>
            <p id="titulo"><b>{{NOME_DA_EMPRESA}}</b></p>
            <p><strong class="color">Nome do Cliente:</strong> <?php echo esc($ordem->nome)?></p>
            <p><strong class="color">Codigo da Ordem:</strong> <?php echo esc($ordem->codigo)?></p>
            <p><strong class="color">Situação da Ordem:</strong> <?php echo ($ordem->situacao)?></p>
            <p><strong class="color">Ordem aberta por:</strong> <?php echo ($ordem->usuario_abertura)?></p>
            <p><strong class="color">Técnico Responsável:</strong> <?php echo ($ordem->usuario_responsavel != null ? $ordem->usuario_responsavel : 'Não Definido!')?></p>

            <?php if($ordem->situacao === 'encerrada'): ?>

                <p><strong class="color">Ordem encerrada por:</strong> <?php echo ($ordem->usuario_encerramento)?></p>

            <?php endif; ?>   

            <p><strong class="color">Ordem criada:</strong> <?php echo $ordem->criado_em->humanize(); ?></p>
            <p><strong class="color">Ordem atualizada:</strong> <?php echo $ordem->atualizado_em->humanize(); ?></p>

        </div>

        <table id="tabela">
            <thead>
                <tr>
                	<th class="celula_head">Cod</th>
                    <th class="celula_head">Quant</th>
                    <th class="celula_head">Unid</th>
                    <th class="celula_head">Descrição do Produto</th>
                    <th class="celula_head">Valor Unit</th>
                    <th class="celula_head">Total</th>
                </tr>
            </thead>
            <tbody>
                {{LOOP_IN}}
                <tr>
                	<td class="celula_center">{{COD}}</td>
                    <td class="celula_center">{{QUANTIDADE}}</td>
                    <td class="celula_center">{{UNIDADE}}</td>
                    <td class="celula">{{DESCRIÇÃO}}</td>
                    <td class="celula">R$ {{VALOR_UNITARIO}}</td>
                    <td class="celula">R$ {{TOTAL}}</td>
                </tr>
                {{LOOP_FIM}}
            </tbody>
        </table>
        <div id="rodape-final">
            <hr size="1" color=black>
            <p id="align-right"><b>TOTAL GERAL: </b>R$ {{TOTAL_GERAL}}</p>
        </div>
    </body>
</html>
