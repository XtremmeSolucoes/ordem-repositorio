<?= $this->extend('Layout/principal') ?>


<?= $this->section('titulo') ?>
<?= $titulo ?>
<?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!-- aqui os estilos da view -->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>
<!-- aqui os conteudos da view  -->
<div class="row">
    <div class="col-lg-12">
        <div class="block">

            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Detalhes da Ordem</button>
                </li>
                <?php if (isset($ordem->transacao)) : ?>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Transações da Ordem</button>
                    </li>

                <?php endif; ?>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="user-block text-center">

                        <div class="user-title mb-4">

                            <h4 class="card-title mt-2"><?php echo esc($ordem->nome); ?></h4>
                            <span>Ordem: <?php echo esc($ordem->codigo); ?></span>

                        </div>

                        <p class="contributions mt-0"><?php echo $ordem->exibeSituacao(); ?></p>
                        <p class="contributions mt-0">Aberta por:<b> <?php echo esc($ordem->usuario_abertura); ?></b></p>
                        <p class="contributions mt-0">Responsável Tecnico:<b> <?php echo esc($ordem->usuario_responsavel !== null ? $ordem->usuario_responsavel : 'Não Definido'); ?></b></p>

                        <?php if ($ordem->situacao === 'encerrada') : ?>

                            <p class="contributions mt-0">Encerrada por:<b> <?php echo esc($ordem->usuario_encerramento); ?></b></p>

                        <?php endif; ?>

                        <p class="card-text">Criado: <?php echo $ordem->criado_em->humanize(); ?></p>
                        <p class="card-text">Atualizado: <?php echo $ordem->atualizado_em->humanize(); ?></p>

                        <hr class="border-secondary">

                        <?php if ($ordem->itens === null) : ?>

                            <div class="contributions py-3">

                                <p>Nenhum item foi adicionado à Ordem</p>

                                <?php if ($ordem->situacao === 'aberta') : ?>

                                    <a class="btn btn-outline-info btn-sm" href="<?php echo site_url("ordensordens/ordens/$ordem->codigo") ?>">Adiconar Item</a>

                                <?php endif; ?>

                            </div>

                        <?php else : ?>

                            <div class="table-responsive my-5">

                                <table class="table table-bordered text-left">
                                    <thead>
                                        <tr>
                                            <th scope="col">Item</th>
                                            <th scope="col">Tipo</th>
                                            <th scope="col">Preço</th>
                                            <th scope="col">Qtde</th>
                                            <th scope="col">Subtotal</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $valorProdutos = 0;
                                        $valorServicos = 0;

                                        ?>

                                        <?php foreach ($ordem->itens as $item) : ?>
                                            <?php

                                            if ($item->tipo === 'produto') {

                                                $valorProdutos += $item->preco_venda * $item->item_quantidade;
                                            } else {

                                                $valorServicos += $item->preco_venda * $item->item_quantidade;
                                            }


                                            ?>
                                            <tr>
                                                <th scope="row"><?php echo ellipsize($item->nome, 32, .5); ?></th>
                                                <td><?php echo esc(ucfirst($item->tipo)); ?></td>
                                                <td>R$ <?php echo esc(number_format($item->preco_venda, 2)); ?></td>
                                                <td><?php echo $item->item_quantidade; ?></td>
                                                <td>R$ <?php echo esc(number_format($item->item_quantidade * $item->preco_venda, 2)); ?></td>
                                                
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="text-right font-weight-bold" colspan="4">
                                                <label>Valor Produto:</label>
                                            </td>
                                            <td class="text-rigth font-weight-bold">R$ <?php echo esc(number_format($valorProdutos, 2)); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right font-weight-bold" colspan="4">
                                                <label>Valor Serviços:</label>
                                            </td>
                                            <td class="text-rigth font-weight-bold">R$ <?php echo esc(number_format($valorServicos, 2)); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right font-weight-bold" colspan="4">
                                                <label>Valor Desconto:</label>
                                            </td>
                                            <td class="text-rigth font-weight-bold">R$ <?php echo esc(number_format($ordem->valor_desconto, 2)); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right font-weight-bold" colspan="4">
                                                <label>Valor Total com Desconto:</label>
                                            </td>
                                            <td class="text-rigth font-weight-bold">R$ 


                                                <?php 

                                                $valorItens = $valorServicos + $valorProdutos;        

                                                echo esc(number_format($valorItens - $ordem->valor_desconto, 2)); ?>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-right font-weight-bold" colspan="4">
                                                <label>Valor Total da Ordem:</label>
                                            </td>
                                            <td class="text-rigth font-weight-bold">R$ <?php echo esc(number_format($valorServicos + $valorProdutos, 2)); ?></td>
                                        </tr>
                                    </tfoot>
                                </table>

                            </div>

                        <?php endif; ?>

                    </div>
                </div>

                <?php if (isset($ordem->transacao)) : ?>

                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        Transações
                    </div>

                <?php endif; ?>
            </div>

            <!-- Example single danger button -->
            <div class="btn-group">
                <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <?php if ($ordem->situacao === 'aberta') : ?>

                        <a class="dropdown-item" href="<?php echo site_url("ordens/editar/$ordem->codigo"); ?>">Editar Ordem</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo site_url("ordens/encerrar/$ordem->codigo"); ?>">Encerrar Ordem</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo site_url("ordensitens/itens/$ordem->codigo"); ?>">Gerenciar itens da Ordem</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo site_url("ordens/responsavel/$ordem->codigo"); ?>">Definir técnico responsável</a>
                        <div class="dropdown-divider"></div>

                    <?php endif; ?>

                    <a class="dropdown-item" href="<?php echo site_url("ordensevidencias/evidencias/$ordem->codigo"); ?>">Evidências da Ordem</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo site_url("ordens/email/$ordem->codigo"); ?>">Enviar por E-mail</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo site_url("ordens/gerapdf/$ordem->codigo"); ?>">Gerar PDF</a>
                    <div class="dropdown-divider"></div>

                    <?php if ($ordem->deletado_em === null) : ?>

                        <a class="dropdown-item" href="<?php echo site_url("ordens/excluir/$ordem->codigo"); ?>">Excluir Ordem</a>

                    <?php endif; ?>

                </div>
            </div>

            <a href="<?php echo site_url("ordens"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>

        </div> <!-- ./ block -->
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- aqui os scripts da view  -->
<?= $this->endSection() ?>